<?php

namespace App\Http\Controllers;

use App\Http\Resources\TestmoniaResource;
use App\Models\Testmonia;
use Illuminate\Http\Request;

class TestMoniaController extends Controller
{
    public function index(){
        $tests=Testmonia::all();
        return response()->json([
            'status' =>'success',
            'massage' =>'all testmonias',
            'data' => TestmoniaResource::collection($tests), 
        ]);
    }
}
