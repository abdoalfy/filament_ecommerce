<?php

namespace App\Http\Controllers;

use App\Models\AttributeProduct;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class CheckoutController extends Controller
{
    protected $lang = 2;
    protected $cartItems;
    protected $products = [];
    protected $totalPrice = 0;
    protected $order;
    protected $currency;
    protected $myFatoorah;

    public function __construct(MyFatoorahController $fatoorahController) {
        $this->myFatoorah = $fatoorahController;
    }



        /**
     * Create a new order for the cart items and add them to the order items table
     * then clear the cart itself from db
     * @param \Illuminate\Http\Request $request
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Get the language header
        $this->lang = $request->header('Accept-Language', 2);
        App::setlocale($this->lang == 2 ? 'ar' : 'en');

        // Check if the cart is empty
        $this->checkIfCartEmpty();
        
        // Calculate total price
        $this->calculateTotalPrice();

        // Check if the user have any open [pending] orders before
       // $this->checkIfUserHasPendingOrder();

        // Make sure there are products 
        if (count($this->products) == 0)
            return response()->json(['status' => 'error', 'message' => 'Cart is empty', 'data' => null], Response::HTTP_NO_CONTENT);
            
        // Create Order
        $this->createOrder();

        // Create Order Items
        $this->saveOrderItems();

        $result = $this->payMyFatoorahOrder();

        return response()->json(['status' => 'success', 'message' => '', 'data' => $result]);
    }



     /**
     * Check if the cart is empty
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    protected function checkIfCartEmpty()
    {
        $this->cartItems = Cart::where('user_id', auth()->user()->id)->get();

        if ($this->cartItems->isEmpty())
            return response()->json(['status' => 'error', 'message' => 'Cart is empty', 'data' => null]);
        return false;
    }



    /**
     *  Calculate total price of the products in the cart
     * @return void
     */
    protected function calculateTotalPrice()
    {
        foreach ($this->cartItems as $item) {
            $productattribute=AttributeProduct::where('id',$item->attribute_product_id)->first();
            $product = Product::where('id', $productattribute->product_id)->first();

            if($product) {
                $product->quantity = $item->quantity;
                $this->products [] = $product;
                $this->totalPrice += ($product->base_price + $$productattribute->extra_price ) * $product->quantity;
            }
        }
    }



     /**
     * Create new order
     * @return void
     */
    protected function createOrder()
    {
        $this->order = Order::create([
            'user_id' => auth()->user()->id,
            'total' => $this->totalPrice,
            'status' => 'pending', 
        ]);
    }


     /**
     * Create the order items in the DB
     * @return void
     */
    protected function saveOrderItems()
    {
        foreach ($this->products as $product) {
            OrderItem::create([
                'order_id' => $this->order->id,
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'price' => $product->price,
            ]);
        } 
    }




     /**
     * Start the paying process
     * @return array|mixed|\Illuminate\Http\JsonResponse
     */
    protected function payMyFatoorahOrder()
    {
        
        // Proceed with payment via MyFatoorah
        $invoiceItems = [];
        $this->currency = 'USD';
        try {
           
            //Fill invoice item array
            if(count($this->products) > 0) {
                foreach ($this->products as $item) {
                    $invoiceItems[] = [
                        'ItemName'  => $item->adData->where('lang_id', $this->lang)->first()->name,
                        'Quantity'  => $item->quantity,
                        'UnitPrice' => $item->price,
                    ];
                }
            }

            // Prepare the payment loadout data
            $paymentData = [
                'CustomerName'       => auth()->user()->name . " " . auth()->user()->lastname,
                'InvoiceValue'       => $this->totalPrice,
                'NotificationOption' => 'LNK',
                'DisplayCurrencyIso' => $this->currency->code ?? 'EGP',
                'CustomerEmail'      => auth()->user()->email,
                'CallBackUrl'        => route('myfatoorah.success'),
                'ErrorUrl'           => route('myfatoorah.error'),
                'MobileCountryCode'  => auth()->user()->dialing_code ?? "+20",
                'CustomerMobile'     => auth()->user()->phone,
                'Language'           => $this->lang == 2? "ar" : 'en',
                'CustomerReference'  => $this->order->id,
                'InvoiceItems'       => $invoiceItems,
                'UserDefinedField'   => auth()->user()->id,
                'SourceInfo'         => 'Laravel ' . app()::VERSION . ' - MyFatoorah',
            ];

            // send user to myFatoorah payment page
            $payment = $this->myFatoorah->mfObj->getInvoiceURL($paymentData, 0, $this->order->id);
          
            return [
                'invoiceURL' => $payment['invoiceURL']
            ];
            
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage(), 'data' => null]);
        }
    }



    
}
