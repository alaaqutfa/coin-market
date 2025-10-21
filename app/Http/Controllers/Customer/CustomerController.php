<?php

namespace App\Http\Controllers\Customer;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class CustomerController extends Controller
{
    public function home(){
        $products = Product::whereNotNull('image_path')->latest()->paginate(27);
        return view('customer.product',compact('products'));
    }
}
