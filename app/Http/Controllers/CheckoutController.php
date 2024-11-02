<?php

namespace App\Http\Controllers;

use App\Models\AttributeProduct;
use App\Models\Cart;
use App\Models\Copoun;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderPayment;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;


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

    public function index(Request $request)
    {
        // Set language
        $this->lang = $request->header('Accept-Language', 2);
        App::setLocale($this->lang == 2 ? 'ar' : 'en');

        // Check if the cart is empty
        if ($this->checkIfCartEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Cart is empty', 'data' => null]);
        }

        // Calculate total price
        $this->calculateTotalPrice($request);

        // Create Order
        $this->createOrder();

        // Save Order Items
        $this->saveOrderItems();

        // Process payment
        $result = $this->payMyFatoorahOrder($request);

        return response()->json(['status' => 'success', 'message' => '', 'data' => $result]);
    }

    protected function checkIfCartEmpty()
    {
        $this->cartItems = Cart::where('user_id', auth()->user()->id)->get();
        return $this->cartItems->isEmpty();
    }

    protected function calculateTotalPrice(Request $request)
    {
        // Calculate base total price from cart items
        foreach ($this->cartItems as $item) {
            $productAttribute = AttributeProduct::where('id', $item->attribute_product_id)->first();
            $product = Product::where('id', $productAttribute->product_id)->first();
            if ($product) {
                $product->quantity = $item->quantity;
                $this->products[] = $product;
                $this->totalPrice += ($product->base_price + $productAttribute->extra_price) * $product->quantity;
            }
            $productAttribute->quantity -=$product->quantity;
            $productAttribute->save();
        }

        // Apply coupon if available
        if ($request->has('copoun')) {
            $coupon = $this->validateCoupon($request->input('copoun'));

            if ($coupon) {
                // Adjust total price based on coupon type
                if ($coupon->type === 'value') {
                    $this->totalPrice -= $coupon->value;
                } elseif ($coupon->type === 'percentage') {
                    $this->totalPrice -= ($this->totalPrice * ($coupon->value / 100));
                }
                // Ensure total price doesn't go below zero
                $this->totalPrice = max(0, $this->totalPrice);

                // Decrement the coupon limit
                $coupon->limit -= 1;
                $coupon->save();
            }
        }
    }

    protected function validateCoupon($couponCode)
    {
        $coupon = Copoun::where('copoun', $couponCode)->first();

        if (!$coupon) {
            return response()->json(['error' => 'Invalid coupon code.'], 400);
        }

        // Check date validity
        $now = Carbon::now();
        if ($now->lt($coupon->start_date) || $now->gt($coupon->ena_date)) {
            return response()->json(['error' => 'Coupon is not within the valid date range.'], 400);
        }

        // Check if coupon limit is exceeded
        if ($coupon->limit <= 0) {
            return response()->json(['error' => 'Coupon limit has been reached.'], 400);
        }

        return $coupon;
    }

    protected function createOrder()
    {
        $this->order = Order::create([
            'user_id' => auth()->user()->id,
            'total' => $this->totalPrice,
            'status' => 'pending', 
        ]);
    }

    protected function saveOrderItems()
    {
        foreach ($this->products as $product) {
            OrderItem::create([
                'order_id' => $this->order->id,
                'product_id' => $product->id,
                'quantity' => $product->quantity,
                'price' => $product->base_price,
            ]);
        }
    }

     /**
     * Start the paying process
     * @return array|mixed|\Illuminate\Http\JsonResponse
     */
    protected function payMyFatoorahOrder($request)
    {
        $invoiceItems = [];
        $this->currency = 'EGP';
        $totalInvoiceValue = 0; // Initialize total for validation
    
        try {
            // Populate invoice items array and calculate total
            if (count($this->products) > 0) {
                foreach ($this->products as $item) {
                    // Find the associated attribute product price
                    $productAttribute = AttributeProduct::where('id', $item->attribute_product_id)->first();
                    $attributeExtraPrice = $productAttribute ? $productAttribute->extra_price : 0;
            
                    // Calculate the full unit price including the attribute extra price
                    $unitPrice = $item->base_price + $attributeExtraPrice;
                    $lineTotal = $unitPrice * $item->quantity;
                    $totalInvoiceValue += $lineTotal; // Accumulate total
                    
                    $invoiceItems[] = [
                        'ItemName'  => $item->name,
                        'Quantity'  => $item->quantity,
                        'UnitPrice' => $unitPrice,
                    ];
                }
            }
    
            // Set InvoiceValue to the calculated totalInvoiceValue
            $paymentData = [
                'CustomerName'       => auth()->user()->name . " " . auth()->user()->lastname,
                'InvoiceValue'       => $totalInvoiceValue,  // Updated to match item totals
                'NotificationOption' => 'LNK',
                'DisplayCurrencyIso' => $this->currency->code ?? 'EGP',
                'CustomerEmail'      => auth()->user()->email,
                'CallBackUrl'        => route('myfatoorah.success'),
                'ErrorUrl'           => route('myfatoorah.error'),
                'MobileCountryCode'  => auth()->user()->dialing_code ?? "+20",
                'CustomerMobile'     => auth()->user()->phone,
                'Language'           => $this->lang == 2 ? "ar" : 'en',
                'CustomerReference'  => $this->order->id,
                'InvoiceItems'       => $invoiceItems,
            ];
    
            $payment = $this->myFatoorah->mfObj->getInvoiceURL($paymentData, $request->payment_id, $this->order->id);


            //create order payment data
            OrderPayment::create([
                'order_id' => $this->order->id,
                'invoice_id' => $payment['invoiceId'],
                'invoice_url' => $payment['invoiceURL'],
                'user_id'=> auth()->user()->id,
                'price'=>$totalInvoiceValue,
            ]);

            // Send user to MyFatoorah payment page
            return [
                'invoiceURL' => $payment['invoiceURL'],
                'invoiceID' => $payment['invoiceId'],
            ];
        } catch (Exception $ex) {
            return response()->json(['status' => 'error', 'message' => $ex->getMessage(), 'data' => null]);
        }
    }


    protected function callAPI($url, $apiKey, $data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "Authorization: Bearer $apiKey",
                "Content-Type: application/json",
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
        ]);

        $response = curl_exec($curl);

        // Handle errors
        if (curl_errno($curl)) {
            throw new \Exception('API Request Error: ' . curl_error($curl));
        }

        curl_close($curl);

        return $response;
    }


    public function success(Request $request){
        $apikey=config('myfatoorah.api_key');
        $requestData=[
            'key'=>$request->paymentId,
            'Keytype'=>'paymentId',
        ];
        $respone=$this->callAPI('https://apitest.myfatoorah.com/v2/getPaymentStatus',$apikey,$requestData);
        $respone=json_decode($respone);

        if(!isset($respone->Data->InvoiceId)){
            return response()->json([
                'status'=>false,
                'error'=>'error'
            ]);
        }

        $paymnetdata=OrderPayment::where('invoice_id',$respone->Data->InvoiceId)->first();
        if($respone->IsSuccess == 'true'){
            if($respone->Data->InvoiceStatus == 'Paid')
            if($paymnetdata->price == $respone->Data->InvoiceValue)
            $paymnetdata->status="paid";
            $paymnetdata->isSuccess="true";
            $paymnetdata->transactionData=$respone->Data->CreatedDate;
            $paymnetdata->save();
            $order=Order::where('id',$paymnetdata->order_id)->first();
            $order->status='paid';
            $order->save();
            return response()->json(['status' => 'success', 'message' => 'Payment done successfylly' , 'data'=> null]);
        }
        return response()->json(['status' => 'error', 'message' => 'an error occur' , 'data'=> null]);
    }



    public function error(Request $request){
        $apikey=config('myfatoorah.api_key');
        $requestData=[
            'key'=>$request->paymentId,
            'Keytype'=>'paymentId',
        ];
        $respone=$this->callAPI('https://apitest.myfatoorah.com/v2/getPaymentStatus',$apikey,$requestData);
        $respone=json_decode($respone);

        if(!isset($respone->Data->InvoiceId)){
            return response()->json([
                'status'=>false,
                'error'=>'error'
            ]);
        }
        $paymnetdata=OrderPayment::where('invoice_id',$respone->Data->InvoiceId)->first();
        if($respone->IsSuccess == 'true'){
            if($respone->Data->InvoiceStatus == 'Pending')
            if($paymnetdata->price == $respone->Data->InvoiceValue)
            $paymnetdata->status='canceled';
            $paymnetdata->isSuccess="true";
            $paymnetdata->transactionData=$respone->Data->CreatedDate;
            $paymnetdata->save();
            $order=Order::where('id',$paymnetdata->order_id)->first();
            $order->status='failed';
            $order->save();
            return response()->json(['status' => 'error', 'message' => 'Payment not paied' , 'data'=> null]);
        }
    }


    //send copoun in the cart page before checkout the ensure it work or not ---not decrement it limit as
    //the fornt end will send me it again during crateorder (checkout process) then her i will decrement the limit
    public function applyCoupon(Request $request) {
        $coupon = Copoun::where('copoun', $request->input('copoun'))->first();
        if ($coupon) {
            $validCoupon = $this->validateCoupon($request->input('copoun'));
            if ($validCoupon) {
                $carts = Cart::where('user_id', auth()->user()->id)->get();
                $totalPrice = 0;
                $totalPriceBeforeDiscount = 0;
                
                foreach ($carts as $item) {
                    $productAttribute = AttributeProduct::where('id', $item->attribute_product_id)->first();
                    $product = Product::where('id', $productAttribute->product_id)->first();
                    if ($product) {
                        $product->quantity = $item->quantity;
                        $totalPriceBeforeDiscount += ($product->base_price + $productAttribute->extra_price) * $product->quantity;
                        $totalPrice += ($product->base_price + $productAttribute->extra_price) * $product->quantity;
                    }
                }
                
                // Apply the coupon discount
                if ($coupon->type === 'value') {
                    $totalPrice -= $coupon->value;
                } elseif ($coupon->type === 'percentage') {
                    $totalPrice -= ($totalPrice * ($coupon->value / 100));
                }
                
                // Ensure total price doesn't go below zero
                $totalPrice = max(0, $totalPrice);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Coupon applied successfully',
                    'data' => [
                        'price_after_copoun' => $totalPriceBeforeDiscount,
                        'price_before_copoun' => $totalPrice,
                        'coupon' => $coupon,
                    ],
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'This is an invalid coupon',
                'data' => [],
            ]);  
        }
        
        return response()->json([
            'status' => 'error',
            'message' => 'Coupon not found',
            'data' => [],
        ]);
    }

}
