<?php
namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function home(Request $request)
    {
        // تحديد نطاق التاريخ
        $startDate = $request->input('start_date', now()->subWeek()->toDateString());
        $endDate   = $request->input('end_date', now()->toDateString());

        // جلب آخر 20 منتج حسب سجلات الباركود
        $latestProducts = Product::query()
            ->whereNotNull('image_path')
            ->whereExists(function ($query) use ($startDate, $endDate) {
                $query->select(DB::raw(1))
                    ->from('product_barcode_logs')
                    ->whereColumn('product_barcode_logs.barcode', 'products.barcode')
                    ->whereBetween('product_barcode_logs.created_at', [
                        $startDate . ' 00:00:00',
                        $endDate . ' 23:59:59',
                    ]);
            })
            ->with('category')
            ->select('products.*')
            ->addSelect([
                'last_barcode_scan' => DB::table('product_barcode_logs')
                    ->select('created_at')
                    ->whereColumn('barcode', 'products.barcode')
                    ->orderByDesc('created_at')
                    ->limit(1),
            ])
            ->orderByDesc('last_barcode_scan')
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
        // إذا كان طلب لعرض منتجات فئة محددة مع pagination
        if ($request->filled('load_category')) {
            $categoryId = $request->load_category;
            $page       = $request->page ?? 1;
            $products   = Product::query()
                ->whereNotNull('image_path')
                ->where('category_id', $categoryId)
            // ->with('brand')
                ->latest()
                ->paginate(40, ['*'], 'page', $page);

            $category = Category::find($categoryId);

            return view('customer.partials.category-products', compact('products', 'category'));
        }

        // بناء الـ Query للفلترة العادية (اسم، سعر، وزن، براند، فئة)
        $query = Product::query()
            ->whereNotNull('image_path')
            ->with('category', 'brand');

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }
        if ($request->filled('weight')) {
            $query->where('weight', $request->weight);
        }
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $products = $query->latest()->paginate(40);

        // لو كان الطلب AJAX → نعيد الـ HTML الخاص بنتائج الفلترة فقط
        if ($request->ajax() || $request->wantsJson()) {
            return view('customer.partials.filtered-products-list', compact('products'))->render();
        }

        // غير AJAX (نادر) → نعيد الصفحة الكاملة كما كانت سابقاً
        $allBrands     = Brand::all();
        $allCategories = Category::all();
        $filters       = $request->all();
        return view('customer.product', compact('products', 'allBrands', 'allCategories', 'filters'));
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
