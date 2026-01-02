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
        $query = Category::query()->with(['products' => function ($productQuery) use ($request) {
            // تطبيق الفلاتر على المنتجات داخل كل فئة
            $productQuery->whereNotNull('image_path');
                // ->whereNotNull('brand_id');

            // الفلترة حسب الاسم
            if ($request->name) {
                $productQuery->where('name', 'like', '%' . $request->name . '%');
            }

            // الفلترة حسب السعر
            if ($request->price) {
                $productQuery->where('price', $request->price);
            }

            // الفلترة حسب الوزن
            if ($request->weight) {
                $productQuery->where('weight', $request->weight);
            }

            // الفلترة حسب العلامة التجارية
            if ($request->brand) {
                $productQuery->where('brand_id', $request->brand);
            }

            // ترتيب المنتجات داخل كل فئة
            $productQuery->latest();
        }]);

        // فلترة الفئات التي تحتوي على منتجات بعد تطبيق الفلاتر
        $query->whereHas('products', function ($productQuery) use ($request) {
            $productQuery->whereNotNull('image_path');
                // ->whereNotNull('brand_id');

            if ($request->name) {
                $productQuery->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->price) {
                $productQuery->where('price', $request->price);
            }

            if ($request->weight) {
                $productQuery->where('weight', $request->weight);
            }

            if ($request->brand) {
                $productQuery->where('brand_id', $request->brand);
            }
        });

        // فلترة حسب فئة محددة
        if ($request->category) {
            $query->where('id', $request->category);
        }

        $filters       = $request->all();
        $categories    = $query->orderBy('name')->get();
        $allBrands     = Brand::all();
        $allCategories = Category::all();

        return view('customer.product', compact('categories', 'allBrands', 'allCategories', 'filters'));
    }

    public function filter(Request $request)
    {
        $query = Category::query()->with(['products' => function ($productQuery) use ($request) {
            $productQuery->whereNotNull('image_path');
            // ->whereNotNull('brand_id');

            if ($request->name) {
                $productQuery->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->price) {
                $productQuery->where('price', $request->price);
            }

            if ($request->weight) {
                $productQuery->where('weight', $request->weight);
            }

            if ($request->brand) {
                $productQuery->where('brand_id', $request->brand);
            }

            $productQuery->latest();
        }]);

        // فلترة الفئات التي تحتوي على منتجات بعد تطبيق الفلاتر
        $query->whereHas('products', function ($productQuery) use ($request) {
            $productQuery->whereNotNull('image_path');
                // ->whereNotNull('brand_id');

            if ($request->name) {
                $productQuery->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->price) {
                $productQuery->where('price', $request->price);
            }

            if ($request->weight) {
                $productQuery->where('weight', $request->weight);
            }

            if ($request->brand) {
                $productQuery->where('brand_id', $request->brand);
            }
        });

        if ($request->category) {
            $query->where('id', $request->category);
        }

        $filters    = $request->all();
        $categories = $query->orderBy('name')->get();

        return view('customer.partials.categories-products', compact('categories', 'filters'));
    }

    public function show($id)
    {
        $product = Product::with('category', 'brand')->findOrFail($id);
        return view('customer.show', compact('product'));
    }
}
