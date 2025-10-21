<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;

class CustomerController extends Controller
{
    public function home()
    {
        $products = Product::whereNotNull('image_path')->latest()->paginate(27);
        return view('customer.product', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with('category', 'brand')->findOrFail($id);

        // لو عرض للواجهة الأمامية بالـ Blade
        return view('customer.show', compact('product'));

        // إذا API JSON فقط:
        // return response()->json($product);
    }
}
