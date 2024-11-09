<?php

namespace App\Http\Controllers;

use App\Http\Resources\PaymentMethodsResource;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodsController extends Controller
{
    public function allPaymentMethods(){
        $payment=PaymentMethod::where('status','active')->get();
        return response()->json([
            'status'=>'active',
            'message'=>'all active payment methods',
            'data'=> PaymentMethodsResource::collection($payment),
        ]);
    }
}
