<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Models\Favorites;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function index(){
        $favorietes=Favorites::with('user','product')->get();
        return response()->json([
            'status' => 'succcess',
            'message' => 'all users favorites',
            'data' => FavoriteResource::collection($favorietes),
        ]);
    }


    public function changeFavorite(Request $request){
        $validator=Validator::make($request->all(),[
            'product_id' =>'required|exists:products,id'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'product not found',
                'data' => [],
            ]);
        };
        $user=auth()->user()->id;
        $product_id=$request->product_id;
        $favorite=Favorites::where('product_id',$product_id)->where('user_id',$user)->first();
        if($favorite){
            $favorite->delete();
        }else{
            $favorite=Favorites::create([
                'user_id' => $user,
                'product_id' => $product_id,
            ]);
        }
        return response()->json([
            'status' => 'succcess',
            'message' => 'product status changed successfully',
            'data' => [],
        ]);
    }



    public function userFav() {
        $user = auth()->user()->id;
        $favorites = Favorites::with('user', 'product')->where('user_id', $user)->get();
    
        if ($favorites->isNotEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'User favorites retrieved successfully.',
                'data' => FavoriteResource::collection($favorites),
            ]);
        }
    
        return response()->json([
            'status' => 'success',
            'message' => 'No user favorites found.',
            'data' => [],
        ]);
    }
}
