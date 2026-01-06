<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
// use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProductBarcodeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::whereNotNull('image_path')->latest()->paginate(50);
        return response()->json($products);
    }

    public function list(Request $request)
    {
        $query = Product::query();

        // الفلترة حسب الباركود
        if ($request->barcode) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        // الفلترة حسب الاسم
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // الفلترة حسب السعر
        if (isset($request->price)) {
            $query->where('price', $request->price);
        }

// الفلترة حسب الوزن
        if (isset($request->weight)) {
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

        // مع صورة
        if ($request->have_image) {
            $query->whereNotNull('image_path');
        }

        // بدون صورة
        if ($request->no_image) {
            $query->whereNull('image_path');
        }

        // فلترة التاريخ حسب النطاق المخصص (من تاريخ - إلى تاريخ)
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        }

        // فلترة تاريخ اليوم
        elseif ($request->has('date_today') && $request->date_today) {
            $query->whereDate('created_at', today());
        }

        // فلترة تاريخ البارحة
        elseif ($request->has('date_yesterday') && $request->date_yesterday) {
            $query->whereDate('created_at', today()->subDay());
        }

        // فلترة آخر أسبوع
        elseif ($request->has('date_week') && $request->date_week) {
            $query->whereBetween('created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة آخر شهر
        elseif ($request->has('date_month') && $request->date_month) {
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة حسب سجلات الباركود - الجزء الجديد
        if ($request->barcode_date_from && $request->barcode_date_to) {
            $query->whereHas('barcodeLogs', function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->barcode_date_from . ' 00:00:00',
                    $request->barcode_date_to . ' 23:59:59',
                ]);
            });
        }

        // الترتيب
        if ($request->has('alphabetical') && $request->alphabetical) {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        // الترتيب من الأحدث إلى الأقدم
        $filters  = $request->all();
        $products = $query->paginate(60)->appends($filters);
        $products->withPath(url('/admin/products'));
        $categories = Category::all();
        $brands     = Brand::all();
        return view('products.view', compact('products', 'filters', 'categories', 'brands'));
    }

    public function filter(Request $request)
    {
        $query = Product::query();

        // الفلترة حسب الباركود
        if ($request->barcode) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        // الفلترة حسب الاسم
        if ($request->name) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // الفلترة حسب السعر
        if (isset($request->price)) {
            $query->where('price', $request->price);
        }

// الفلترة حسب الوزن
        if (isset($request->weight)) {
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

        // مع صورة
        if ($request->have_image) {
            $query->whereNotNull('image_path');
        }

        // بدون صورة
        if ($request->no_image) {
            $query->whereNull('image_path');
        }

        // فلترة التاريخ حسب النطاق المخصص (من تاريخ - إلى تاريخ)
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        }

        // فلترة تاريخ اليوم
        elseif ($request->has('date_today') && $request->date_today) {
            $query->whereDate('created_at', today());
        }

        // فلترة تاريخ البارحة
        elseif ($request->has('date_yesterday') && $request->date_yesterday) {
            $query->whereDate('created_at', today()->subDay());
        }

        // فلترة آخر أسبوع
        elseif ($request->has('date_week') && $request->date_week) {
            $query->whereBetween('created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة آخر شهر
        elseif ($request->has('date_month') && $request->date_month) {
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة حسب سجلات الباركود
        if ($request->barcode_date_from && $request->barcode_date_to) {
            $query->whereHas('barcodeLogs', function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->barcode_date_from . ' 00:00:00',
                    $request->barcode_date_to . ' 23:59:59',
                ]);
            });
        }

        // الترتيب
        if ($request->has('alphabetical') && $request->alphabetical) {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }

        // الترتيب من الأحدث إلى الأقدم
        $filters  = $request->all();
        $products = $query->paginate(60)->appends($filters);
        $products->withPath(url('/admin/products'));
        $categories = Category::all();
        $brands     = Brand::all();

        return view('products.partials.products-table', compact('products', 'filters', 'categories', 'brands'))->render();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode'     => 'required|unique:products',
            'name'        => 'required|max:255',
            'price'       => 'required|numeric|min:0',
            'symbol'      => 'sometimes|string|max:5',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id'    => 'sometimes|exists:brands,id',
            'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->only([
            'barcode', 'name', 'description', 'price', 'symbol',
            'category_id', 'brand_id', 'weight', 'quantity',
        ]);

        if ($request->hasFile('image')) {
            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product = Product::create($productData);

        $product->load('category', 'brand'); // جلب العلاقة للفئة والبراند

        return response()->json($product, 201);
    }

    public function bulkStore(Request $request)
    {
        $products = $request->input('products', []);

        $barcodes = collect($products)->pluck('barcode')->filter()->unique();

        // اجلب المنتجات الموجودة مسبقاً مع كامل بياناتها
        $existingProducts = Product::whereIn('barcode', $barcodes)->get()->keyBy('barcode');

        $insertData   = [];
        $updatedCount = 0;

        foreach ($products as $p) {
            if (! empty($p['barcode']) && ! empty($p['name'])) {

                $barcode = $p['barcode'];

                // سجل اللوج
                ProductBarcodeLog::create([
                    'barcode' => $barcode,
                    'exists'  => isset($existingProducts[$barcode]), // true إذا موجود
                    'source'  => 'bulkStore',
                ]);

                // إذا كان المنتج موجود → حدث
                if (isset($existingProducts[$barcode])) {
                    $existingProducts[$barcode]->update([
                        'name'        => $p['name'],
                        'price'       => $p['price'] ?? $existingProducts[$barcode]->price,
                        'symbol'      => $p['symbol'] ?? $existingProducts[$barcode]->symbol,
                        'category_id' => $p['category_id'] ?? $existingProducts[$barcode]->category_id,
                        'brand_id'    => $p['brand_id'] ?? $existingProducts[$barcode]->brand_id,
                        'weight'      => $p['weight'] ?? $existingProducts[$barcode]->weight,
                        'quantity'    => $p['quantity'] ?? $existingProducts[$barcode]->quantity,
                    ]);

                    $updatedCount++;
                } else {
                    // غير موجود → أضفه
                    $insertData[] = [
                        'barcode'     => $barcode,
                        'name'        => $p['name'],
                        'price'       => $p['price'] ?? 0,
                        'symbol'      => $p['symbol'] ?? '$',
                        'category_id' => $p['category_id'] ?? null,
                        'brand_id'    => $p['brand_id'] ?? null,
                        'weight'      => $p['weight'] ?? null,
                        'quantity'    => $p['quantity'] ?? 0,
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ];
                }
            }
        }

        if (count($insertData)) {
            Product::insert($insertData);
        }

        return response()->json([
            'success'  => true,
            'inserted' => count($insertData),
            'updated'  => $updatedCount,
            'skipped'  => 0,
        ]);
    }

    public function importProducts()
    {
        Excel::import(new ProductsImport, request()->file('file'));
        return back()->with('success', 'تم رفع وتحديث البيانات بنجاح');
    }

    public function getMissingProducts()
    {
        // الباركودات الموجودة فعلياً في جدول المنتجات
        $existing = Product::pluck('barcode')->toArray();

        // رجع الباركود مع وقت إضافته
        $missing = ProductBarcodeLog::whereNotIn('barcode', $existing)
            ->orderBy('created_at', 'asc')
            ->get(['id', 'barcode', 'created_at'])
            ->map(function ($log) {
                return [
                    'id'       => $log->id,
                    'barcode'  => $log->barcode,
                    'added_at' => $log->created_at->format('Y-m-d H:i:s'), // التاريخ + الدقيقة + الثانية
                ];
            })
            ->toArray();

        return response()->json($missing);
    }

    public function destroyMissing($id)
    {
        $log = ProductBarcodeLog::find($id);

        if (! $log) {
            return response()->json([
                'success' => false,
                'message' => 'السجل غير موجود.',
            ], 404);
        }

        // جلب قيمة الباركود قبل الحذف
        $barcode = $log->barcode;

        // حذف كل السجلات التي تحمل نفس الباركود
        $deletedCount = ProductBarcodeLog::where('barcode', $barcode)->delete();

        return response()->json([
            'success' => true,
            'message' => "تم حذف {$deletedCount} سجل يحمل الباركود {$barcode}.",
        ]);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'barcode'     => 'sometimes|unique:products,barcode,' . $id,
            'name'        => 'sometimes|max:255',
            'price'       => 'sometimes|numeric|min:0',
            'symbol'      => 'sometimes|string|max:5',
            'category_id' => 'sometimes|nullable|exists:categories,id',
            'brand_id'    => 'sometimes|nullable|exists:brands,id',
            'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->only([
            'barcode', 'name', 'description', 'price', 'symbol',
            'category_id', 'brand_id', 'weight', 'quantity',
        ]);

        // التحقق من وجود category_id وإذا كان المستخدم سوبر أدمن يمكنه الإنشاء
        if (empty($productData['category_id']) && $request->role_id == 1 && $request->filled('category_name')) {
            $category = Category::firstOrCreate(
                ['name' => $request->input('category_name')],
                ['description' => $request->input('category_description')]
            );
            $productData['category_id'] = $category->id;
        }

        // التحقق من وجود brand_id وإذا كان المستخدم سوبر أدمن يمكنه الإنشاء
        if (empty($productData['brand_id']) && $request->role_id == 1 && $request->filled('brand_name')) {
            $brand = Brand::firstOrCreate(
                ['name' => $request->input('brand_name')],
                ['logo' => $request->input('brand_logo')]
            );
            $productData['brand_id'] = $brand->id;
        }

        // التعامل مع الصورة
        if ($request->hasFile('image')) {
            if ($product->image_path && Storage::exists($product->image_path)) {
                Storage::delete($product->image_path);
            }
            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product->update($productData);

        $product->load('category', 'brand');

        return response()->json($product);
    }

    public function updateByBarcode(Request $request, $barcode)
    {
        $product = Product::where('barcode', $barcode)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'barcode'     => 'sometimes|unique:products,barcode,' . $product->id,
            'name'        => 'sometimes|max:255',
            'price'       => 'sometimes|numeric|min:0',
            'symbol'      => 'sometimes|string|max:5',
            'category_id' => 'sometimes|exists:categories,id',
            'brand_id'    => 'sometimes|exists:brands,id',
            'image'       => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // أخذ الحقول المسموح تحديثها
        $productData = $request->only([
            'barcode', 'name', 'description', 'price', 'symbol',
            'category_id', 'brand_id', 'weight', 'quantity',
        ]);

        // معالجة الصورة
        if ($request->hasFile('image')) {
            if ($product->image_path && Storage::exists($product->image_path)) {
                Storage::delete($product->image_path);
            }
            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product->update($productData);

        // تحميل العلاقات للفئة والبراند قبل الإرجاع
        $product->load('category', 'brand');

        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // حذف الصورة المرتبطة
        if ($product->image_path && Storage::exists($product->image_path)) {
            Storage::delete($product->image_path);
        }

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }

    public function findByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();

        // سجل اللوج
        ProductBarcodeLog::create([
            'barcode' => $barcode,
            'exists'  => $product ? true : false,
            'source'  => 'api', // أو ممكن تبعتها بالـ request
        ]);

        if (! $product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return response()->json($product);
    }

    private function storeImage($image, $barcode)
    {
        $extension = $image->getClientOriginalExtension();
        $imageName = $barcode . '_' . time() . '.' . $extension;

        $path = $image->storeAs('products', $imageName, 'public');

        return $path;
    }

    public function previewImages(Request $request)
    {
        $files   = $request->file('images');
        $results = [];

        foreach ($files as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // حذف السعر من الاسم لو موجود
            // $cleanName = preg_replace('/ - \d+(\.\d+)?\$/', '', $originalName);

            // البحث عن المنتج
            // $product = Product::where('name', $cleanName)->first();
            $product = Product::where('barcode', $originalName)->first();

            // فقط لو لقى المنتج يكمل
            if ($product) {
                // حفظ مؤقت في مجلد public/tmp_products
                $tmpName = uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/tmp_products'), $tmpName);

                $results[] = [
                    'id'     => $product->id,
                    'name'   => $product->name,
                    'image'  => asset('public/storage/tmp_products/' . $tmpName),
                    'tmp'    => $tmpName,
                    'status' => 'matched',
                ];
            }
        }

        return response()->json($results);
    }

    public function saveImages(Request $request)
    {
        $items = $request->input('items');
        foreach ($items as $item) {
            $product = Product::find($item['id']);
            if ($product && isset($item['tmp'])) {
                $tmpPath = public_path('storage/tmp_products/' . $item['tmp']);
                if (file_exists($tmpPath)) {
                    $extension = pathinfo($tmpPath, PATHINFO_EXTENSION);

                    // اسم الصورة = اسم المنتج + السعر
                    $safeName = Str::slug($product->name . '-' . $product->price, '_');
                    $newName  = $safeName . '.' . $extension;
                    $newPath  = public_path('storage/products/' . $newName);

                    // إنشاء مجلد products إذا مش موجود
                    if (! file_exists(public_path('storage/products'))) {
                        mkdir(public_path('storage/products'), 0777, true);
                    }

                    // حذف الصورة القديمة إن وُجدت
                    if ($product->image_path) {
                        $oldPath = public_path('storage/' . $product->image_path);
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }

                    // نقل الملف الجديد
                    rename($tmpPath, $newPath);

                    // تحديث المنتج في قاعدة البيانات
                    $product->update([
                        'image_path' => 'products/' . $newName,
                    ]);
                }
            }
        }

        $this->clearTmpProducts();

        return response()->json(['status' => 'success']);
    }

    public function clearTmpProducts()
    {
        $tmpDir = public_path('storage/tmp_products');

        if (! file_exists($tmpDir)) {
            return response()->json(['status' => 'error', 'message' => 'مجلد tmp_products غير موجود']);
        }

        $files   = scandir($tmpDir);
        $deleted = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            $filePath = $tmpDir . '/' . $file;
            if (is_file($filePath)) {
                unlink($filePath);
                $deleted[] = $file;
            }
        }

        return response()->json([
            'status'  => 'success',
            'deleted' => $deleted,
            'count'   => count($deleted),
        ]);
    }

    public function cleanUnusedImages()
    {
        $productsDir = public_path('storage/products');

        if (! file_exists($productsDir)) {
            return response()->json(['status' => 'error', 'message' => 'مجلد المنتجات غير موجود']);
        }

        // جميع الصور الموجودة في المجلد
        $files = scandir($productsDir);

        // جميع المسارات المستخدمة فعلاً في قاعدة البيانات
        $usedImages = Product::pluck('image_path')->map(function ($path) {
            return basename($path); // فقط اسم الملف بدون المسار
        })->toArray();

        $deleted = [];

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            if (! in_array($file, $usedImages)) {
                $filePath = $productsDir . '/' . $file;
                if (is_file($filePath)) {
                    unlink($filePath);
                    $deleted[] = $file;
                }
            }
        }

        return response()->json([
            'status'  => 'success',
            'deleted' => $deleted,
            'count'   => count($deleted),
        ]);
    }

    public function exportCatalog(Request $request)
    {
        $ids = $request->input('ids', []);
        $ids = json_decode($ids, true) ?? [];

        if (empty($ids)) {
            return response()->json(['error' => 'لم يتم تحديد أي منتجات'], 400);
        }
        // إذا كان ids نصاً مفصولاً بفواصل، حوله إلى مصفوفة
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_filter($ids); // إزالة القيم الفارغة
        }

        $products = Product::whereIn('id', $ids)
            ->whereNotNull('image_path')
            ->get();

        $html = view('design.design', compact('products'))->render();

        Browsershot::html($html)
            ->setNodeBinary('C:\Program Files\nodejs\node.exe')
            ->setNpmBinary('C:\Program Files\nodejs\npm.cmd')
            ->setChromePath('C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->windowSize(1080, 1080)
            ->timeout(120)
            ->noSandbox()
            ->addChromiumArguments(['--no-sandbox', '--disable-setuid-sandbox'])
            ->save(public_path('catalog.png'));

        return response()->download(public_path('catalog.png'));
    }

    public function showCatalog(Request $request)
    {
        $ids = $request->input('ids', []);
        $ids = json_decode($ids, true) ?? [];

        if (empty($ids)) {
            return response()->json(['error' => 'لم يتم تحديد أي منتجات'], 400);
        }
        // إذا كان ids نصاً مفصولاً بفواصل، حوله إلى مصفوفة
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_filter($ids); // إزالة القيم الفارغة
        }

        $products = Product::whereIn('id', $ids)
            ->whereNotNull('image_path')
            ->get();
        return view('design.design', compact('products'))->render();
    }

}
