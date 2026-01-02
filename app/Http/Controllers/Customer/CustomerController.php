<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function home(Request $request)
    {

        $query = Product::query();
        $query->whereNotNull('image_path');
        // $query->whereNotNull('category_id');
        $query->whereNotNull('brand_id');

        // الفلترة حسب الاسم
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // الفلترة حسب السعر
        if ($request->price) {
            $query->where('price', $request->price);
        }

        // الفلترة حسب الوزن
        if ($request->weight) {
            $query->where('weight', $request->weight);
        }

        // الفلترة حسب الفئة
        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // الفلترة حسب العلامة التجارية
        if ($request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // الترتيب من الأحدث إلى الأقدم
        $filters  = $request->all();
        $products = $query->latest()->paginate(27)->appends($filters);
        $products->withPath(url('/'));
        $categories = Category::all();
        $brands     = Brand::all();
        return view('customer.product', compact('products', 'categories', 'brands','filters'));
    }

    public function filter(Request $request)
    {
        $query = Product::query();
        $query->whereNotNull('image_path');
        // $query->whereNotNull('category_id');
        $query->whereNotNull('brand_id');

        // الفلترة حسب الاسم
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // الفلترة حسب السعر
        if ($request->price) {
            $query->where('price', $request->price);
        }

        // الفلترة حسب الوزن
        if ($request->weight) {
            $query->where('weight', $request->weight);
        }

        // // الفلترة حسب الفئة
        // if ($request->category) {
        //     $query->where('category_id', $request->category);
        // }

        // الفلترة حسب العلامة التجارية
        if ($request->brand) {
            $query->where('brand_id', $request->brand);
        }

        // الترتيب من الأحدث إلى الأقدم
        $filters  = $request->all();
        $products = $query->latest()->paginate(27)->appends($filters);
        $products->withPath(url('/'));

        return view('customer.partials.product-item', compact('products','filters'));
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
