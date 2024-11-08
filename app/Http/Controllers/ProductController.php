<?php

namespace App\Http\Controllers;

use App\Filament\Resources\ProductResource as ResourcesProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function allCategories(){
        $categories=Category::with([
            'children',
            'products.images',
            'products.attributeProducts.attribute',  
            'products.attributeProducts.variant',    
            'products.attributeProducts.images',  
        ])->paginate('10');
        if($categories){
            return response()->json([
                'status'=>'success',
                'message'=>'categories founded successfully',
                'data' => CategoryResource::collection($categories),  // Use CategoryResource collection
                  'meta' => [
                  'current_page' => $categories->currentPage(),
                  'last_page' => $categories->lastPage(),
                  'per_page' => $categories->perPage(),
                  'total' => $categories->total(),
            ]
            ]);
        }
        return response()->json([
            'status'=>'success',
            'message'=>'there are no categories found',
            'data'=> [],
        ]);
    }


    public function singleCategory($slug){
        $category = Category::with([
            'children',
            'products.images',
            'products.attributeProducts.attribute',  
            'products.attributeProducts.variant',    
            'products.attributeProducts.images',  
        ])->where('slug',$slug)->first();
        if($category){
            return response()->json([
                'status'=>'success',
                'message'=>'category founded successfully',
                'data'=> new CategoryResource($category),
            ]);
        }
        return response()->json([
            'status'=>'error',
            'message'=>'category not found',
            'data'=> [],
        ]);
    }


    public function singleProduct($slug){
        $product = Product::with(['images',
        'category',
        'attributes',
        'variants',
        'attributeProducts',
        'attributeProducts.attribute',
        'attributeProducts.variant',
        'attributeProducts.images'])
        ->where('slug',$slug)->first();
        if($product){
            return response()->json([
                'status'=>'success',
                'message'=>'product founded successfully',
                'data'=> new ProductResource($product),
            ]);
        }
        return response()->json([
            'status'=>'error',
            'message'=>'product not found',
            'data'=> [],
        ]);
   
    }


    public function allProducts()
    {
        $products = Product::with('category', 'images', 'attributes', 'variants', 'attributeProducts.attribute', 'attributeProducts.variant', 'attributeProducts.images')
            ->paginate(10);    
        return response()->json([
            'status' => 'success',
            'message' => 'all products',
            'data' => ProductResource::collection($products),
        ]);
    }
    
}
