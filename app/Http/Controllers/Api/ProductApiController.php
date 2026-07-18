<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Http\Resources\ProductResource;

class ProductApiController extends Controller
{
    public function index()
    {
        $products = Product::with('branches')->get();
        return ProductResource::collection($products);
    }
}