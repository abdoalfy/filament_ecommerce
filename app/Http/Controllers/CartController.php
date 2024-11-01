<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;


class CartController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->header('Accept-Language', 2);
        App::setLocale($lang == 2 ? 'ar' : 'en');
        $userId = auth()->user()->id;
        if (!$userId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        // Retrieve cart items with the product and attribute images
        $cartItems = Cart::with(['attributeProduct.product', 'attributeProduct.images'])
                        ->where('user_id', $userId)
                        ->get();
    
        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'success', 'message' => 'Cart is empty', 'data' => []]);
        }
        return CartResource::collection($cartItems);
    }




    //add array of product in one step
    public function addToCart(Request $request)
    {
        $lang = $request->header('Accept-Language', 2) == 2 ? 'ar' : 'en';
        App::setLocale($lang);
        $userId = auth()->user()->id;
    
        // Validate the request input
        $request->validate([
            'items' => 'required|array',
            'items.*.attribute_product_id' => 'required|exists:attribute_product,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);
    
        $cartItems = [];
    
        foreach ($request->items as $item) {
            $attributeProductId = $item['attribute_product_id'];
            $quantity = $item['quantity'];
    
            // Check if the item is already in the cart
            $cartItem = Cart::where('attribute_product_id', $attributeProductId)
                            ->where('user_id', $userId)
                            ->first();
    
            if ($cartItem) {
                // If the item is already in the cart, increment the quantity
                $cartItem->quantity += $quantity;
                $cartItem->save();
            } else {
                // If not in the cart, create a new cart entry
                $cartItem = Cart::create([
                    'attribute_product_id' => $attributeProductId,
                    'user_id' => $userId,
                    'quantity' => $quantity,
                ]);
            }
    
            $cartItems[] = $cartItem;
        }
        // Return the response using CartItemResource
        return response()->json([
            'status' => 'success',
            'message' => 'Products added successfully',
            'data' => CartResource::collection($cartItems),
        ]);
    }
    



    // add product by product logic

    // public function addToCart(Request $request)
    // {
    //     // Set the locale based on the request header
    //     $lang = $request->header('Accept-Language', 2);
    //     App::setlocale($lang == 2 ? 'ar' : 'en');
    
    //     // Get the authenticated user's ID
    //     $userId = auth()->user()->id;
    
    //     // Validate the request input
    //     $request->validate([
    //         'attribute_product_id' => 'required|exists:attribute_product,id',
    //     ]);
    
    //     $attributeProductId = $request->attribute_product_id;
    
    //     // Check if the item is already in the cart
    //     $cartItem = Cart::where('attribute_product_id', $attributeProductId)
    //                     ->where('user_id', $userId)
    //                     ->first();
    
    //     if ($cartItem) {
    //         // If the item is already in the cart, increment the quantity
    //         $cartItem->quantity += 1;
    //         $cartItem->save();
    //     } else {
    //         // If not in the cart, create a new cart entry
    //         $cartItem = Cart::create([
    //             'attribute_product_id' => $attributeProductId,
    //             'user_id' => $userId,
    //             'quantity' => 1,
    //         ]);
    //     }
    //     // Prepare the response data with additional information about the added item
    //     $cartData = new CartResource($cartItem->load(['attributeProduct', 'attributeProduct.product'])); // Load related data
    //     return response()->json([
    //         'status' => 'success',
    //         'message' => 'Product added successfully',
    //         'data' => $cartData,
    //     ]);
    // }


    public function removeFromCart(Request $request)
    {
        // Set the locale based on the request header
        $lang = $request->header('Accept-Language', 2);
        App::setLocale($lang == 2 ? 'ar' : 'en');
    
        // Get the authenticated user's ID
        $userId = auth()->user()->id;
    
        // Validate the request input
        $request->validate([
            'attribute_product_id' => 'required|exists:attribute_product,id',
        ]);
    
        $attributeProductId = $request->attribute_product_id;
    
        // Check if the item exists in the cart
        $cartItem = Cart::where('attribute_product_id', $attributeProductId)
                        ->where('user_id', $userId)
                        ->first();
    
        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                // If quantity is more than 1, decrease the quantity
                $cartItem->quantity -= 1;
                $cartItem->save();
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item quantity decreased successfully',
                    'data' => new CartResource($cartItem)
                ]);
            } else {
                // Otherwise, delete the item from the cart
                $cartItem->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Item removed successfully'
                ]);
            }
        }
        return response()->json(['status' => 'error', 'message' => 'Item not found in cart']);
    }
    


    //the putton x that remove all product not decremnt only one
    public function deleteFromCart(Request $request)
    {
        // Set the locale based on the request header
        $lang = $request->header('Accept-Language', 2);
        App::setLocale($lang == 2 ? 'ar' : 'en');
    
        // Get the authenticated user's ID
        $userId = auth()->user()->id;
    
        // Validate the request input
        $request->validate([
            'attribute_product_id' => 'required|exists:attribute_product,id',
        ]);
    
        $attributeProductId = $request->attribute_product_id;
    
        // Check if the item exists in the cart
        $cartItem = Cart::where('attribute_product_id', $attributeProductId)
                        ->where('user_id', $userId)
                        ->first();

        $othercartItems = Cart::where('user_id', $userId)->first();
    
        if ($cartItem) {
            // Delete the item from the cart
            $cartItem->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Item removed from cart',
                'data' =>  new CartResource($othercartItems)
            ]);
        }
    
        return response()->json([
            'status' => 'error',
            'message' => 'Item not found in cart',
            'data' => null
        ], Response::HTTP_NOT_FOUND);
    }


    public function deleteCart(Request $request)
    {
        $lang = $request->header('Accept-Language', 2);
        App::setlocale($lang == 2 ? 'ar' : 'en');

        $userId = auth()->user()->id;

        $attributeProductId = $request->attribute_product_id;
        // Check if the item exists in the cart
        $cartItem = Cart::where('user_id', $userId)
                        ->first();

        if ($cartItem) {
            // Delete the cart
            $cartItem->delete();
            return response()->json(['status' => 'success', 'message' => 'Cart deleted successfully', 'data' => null]);
        }
        return response()->json(['status' => 'error', 'message' => 'cart not found', 'data' => null], Response::HTTP_NOT_FOUND);
    }
}
