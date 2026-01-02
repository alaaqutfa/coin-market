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
        // جلب آخر 20 منتج بدون التصنيفات
        $latestProducts = Product::query()
            ->whereNotNull('image_path')
            // ->with('category', 'brand')
            ->with('category')
            ->latest()
            ->take(20)
            ->get();

        // جلب الفئات مع آخر 20 منتجات لكل فئة (للعرض المبدئي)
        $categories = Category::query()
            ->with(['products' => function ($query) {
                $query->whereNotNull('image_path')
                      ->latest()
                      ->take(20); // 20 منتجات لكل فئة في العرض الأولي
            }])
            ->whereHas('products', function ($query) {
                $query->whereNotNull('image_path');
            })
            ->when($request->category, function ($query, $categoryId) {
                return $query->where('id', $categoryId);
            })
            ->orderBy('name')
            ->get();

        $filters       = $request->all();
        $allBrands     = Brand::all();
        $allCategories = Category::all();

        return view('customer.product', compact(
            'latestProducts',
            'categories',
            'allBrands',
            'allCategories',
            'filters'
        ));
    }

    public function filter(Request $request)
    {
        // إذا كان طلب فلترة عادي (من شريط البحث)
        if ($request->filled('name') || $request->filled('price') ||
            $request->filled('weight') || $request->filled('brand') ||
            $request->filled('category')) {

            // فلترة المنتجات مباشرة
            $productsQuery = Product::query()
                ->whereNotNull('image_path')
                // ->with('category', 'brand');
                ->with('category');

            if ($request->name) {
                $productsQuery->where('name', 'like', '%' . $request->name . '%');
            }

            if ($request->price) {
                $productsQuery->where('price', $request->price);
            }

            if ($request->weight) {
                $productsQuery->where('weight', $request->weight);
            }

            if ($request->brand) {
                $productsQuery->where('brand_id', $request->brand);
            }

            if ($request->category) {
                $productsQuery->where('category_id', $request->category);
            }

            $products = $productsQuery->latest()->paginate(40);

            return view('customer.partials.filtered-products', compact('products', 'filters'));
        }

        // إذا كان طلب لعرض منتجات فئة محددة مع pagination
        if ($request->filled('load_category')) {
            $categoryId = $request->load_category;
            $page = $request->page ?? 1;

            $products = Product::query()
                ->whereNotNull('image_path')
                ->where('category_id', $categoryId)
                // ->with('brand')
                ->latest()
                ->paginate(40, ['*'], 'page', $page);

            $category = Category::find($categoryId);

            return view('customer.partials.category-products', compact('products', 'category'));
        }

        // الافتراضي: عرض الفئات مع 20 منتجات لكل فئة
        $categories = Category::query()
            ->with(['products' => function ($query) {
                $query->whereNotNull('image_path')
                      ->latest()
                      ->take(20);
            }])
            ->whereHas('products', function ($query) {
                $query->whereNotNull('image_path');
            })
            ->when($request->category, function ($query, $categoryId) {
                return $query->where('id', $categoryId);
            })
            ->orderBy('name')
            ->get();

        $filters = $request->all();

        return view('customer.partials.categories-products', compact('categories', 'filters'));
    }

    public function show($id)
    {
        // $product = Product::with('category', 'brand')->findOrFail($id);
        $product = Product::with('category')->findOrFail($id);
        return view('customer.show', compact('product'));
    }

    // دالة جديدة: عرض جميع منتجات فئة مع pagination
    public function categoryProducts($id, Request $request)
    {
        $category = Category::findOrFail($id);

        $products = Product::query()
            ->whereNotNull('image_path')
            ->where('category_id', $id)
            // ->with('brand')
            ->latest()
            ->paginate(40);

        return view('customer.category', compact('category', 'products'));
    }
}
