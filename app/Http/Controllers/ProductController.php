<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\DailyProductsImport;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductBarcodeLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Browsershot\Browsershot;

class ProductController extends Controller
{
    private function applyFilters(Builder $query, Request $request): void
    {
        // الفلترة حسب الباركود
        if ($request->filled('barcode')) {
            $query->where('barcode', 'like', '%' . $request->barcode . '%');
        }

        // الفلترة حسب الاسم
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // الفلترة حسب السعر
        if ($request->filled('price')) {
            $query->where('price', $request->price);
        }

        // الفلترة حسب الوزن
        if ($request->filled('weight')) {
            $query->where('weight', $request->weight);
        }

        // الفلترة حسب الفئة
        if ($request->category == "noCategory") {
            $query->whereNull('category_id');
        } else if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // الفلترة حسب العلامة التجارية
        if ($request->filled('brand')) {
            $query->where('brand_id', $request->brand);
        }

        // مع صورة
        if ($request->boolean('have_image')) {
            $query->whereNotNull('image_path');
        }

        // بدون صورة
        if ($request->boolean('no_image')) {
            $query->whereNull('image_path');
        }

        // فلترة التاريخ من - إلى (created_at)
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:59',
            ]);
        }

        // فلترة تاريخ اليوم
        elseif ($request->filled('date_today')) {
            $query->whereDate('created_at', today());
        }

        // فلترة تاريخ البارحة
        elseif ($request->filled('date_yesterday')) {
            $query->whereDate('created_at', today()->subDay());
        }

        // فلترة آخر أسبوع
        elseif ($request->filled('date_week')) {
            $query->whereBetween('created_at', [
                now()->subWeek()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة آخر شهر
        elseif ($request->filled('date_month')) {
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
            ]);
        }

        // فلترة حسب سجلات الباركود
        if ($request->filled('barcode_date_from') && $request->filled('barcode_date_to')) {
            $query->whereHas('barcodeLogs', function ($q) use ($request) {
                $q->whereBetween('created_at', [
                    $request->barcode_date_from . ' 00:00:00',
                    $request->barcode_date_to . ' 23:59:59',
                ]);
            });
        }

        // الترتيب
        if ($request->boolean('alphabetical')) {
            $query->orderBy('name', 'asc');
        } else {
            $query->latest();
        }
    }

    public function index()
    {
        $products = Product::whereNotNull('image_path')->whereNotNull('category_id')->latest()->paginate(50);
        return response()->json($products);
    }

    public function list(Request $request)
    {
        $query = Product::query();
        $this->applyFilters($query, $request);

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
        $this->applyFilters($query, $request);

        $filters  = $request->all();
        $products = $query->paginate(60)->appends($filters);
        $products->withPath(url('/admin/products'));
        $categories = Category::all();
        $brands     = Brand::all();

        return view('products.partials.products-table', compact('products', 'filters', 'categories', 'brands'))->render();
    }

    public function jsonFilters(Request $request)
    {
        $query = Product::query();
        $this->applyFilters($query, $request);

        // نستثني المنتجات التي سعرها صفر
        $query->where('price', '>', 0);

        $products = $query->select([
            'id', 'barcode', 'name', 'weight', 'price', 'symbol', 'category_id',
        ])
            ->with(['category:id,name'])
            ->get();

        $formatted = $products->map(function ($product) {
            return [
                'barcode'       => $product->barcode,
                'name'          => $product->name,
                'price'         => $product->price,
                'currency'      => $product->symbol,
                'weight'        => $product->weight,
                'category_id'   => $product->category_id,
                'category_name' => optional($product->category)->name ?? 'بدون فئة',
            ];
        });

        // التجميع حسب الفئة
        $grouped = $formatted->groupBy('category_name')->map(function ($items, $categoryName) {
            $firstItem = $items->first();
            return [
                'category_id'    => $firstItem['category_id'],
                'category_name'  => $categoryName,
                'products_count' => $items->count(),
                'products'       => $items->map(function ($item) {
                    // نحذف category_id و category_name من داخل المنتجات لتجنب التكرار
                    return collect($item)->except(['category_id', 'category_name'])->toArray();
                })->values()->toArray(),
            ];
        })->values()->toArray();

        // بناء سياق تسويقي إضافي ليساعد الذكاء الاصطناعي
        $categoryNames    = collect($grouped)->pluck('category_name')->filter()->toArray();
        $marketingContext = $this->buildMarketingContext($request, $categoryNames, $formatted->count());

        return response()->json([
            'count'             => $formatted->count(),
            'by_category'       => $grouped,
            'marketing_context' => $marketingContext,
        ]);
    }

    private function buildMarketingContext(Request $request, array $categoryNames, int $totalProducts): array
    {
        // ملخص الفلاتر
        $activeFilters = [];
        if ($request->filled('barcode')) {
            $activeFilters[] = "باركود يحتوي على: " . $request->barcode;
        }

        if ($request->filled('name')) {
            $activeFilters[] = "اسم منتج يحتوي على: " . $request->name;
        }

        if ($request->filled('price')) {
            $activeFilters[] = "سعر = " . $request->price;
        }

        if ($request->filled('weight')) {
            $activeFilters[] = "وزن = " . $request->weight;
        }

        if ($request->filled('category')) {
            $activeFilters[] = "فئة محددة";
        }

        if ($request->filled('brand')) {
            $activeFilters[] = "علامة تجارية محددة";
        }

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $activeFilters[] = "نطاق تاريخي محدد";
        }

        $filtersSummary = ! empty($activeFilters)
            ? "تمت فلترة المنتجات بناءً على: " . implode('، ', $activeFilters)
            : "جميع المنتجات (بدون فلترة)";

        // اقتراح جمهور مستهدف بسيط بناءً على أسماء الفئات
        $targetAudience = "عام";
        if (! empty($categoryNames)) {
            $targetAudience = "المهتمون بـ: " . implode('، ', $categoryNames);
        }

        // أفكار رسائل تسويقية ديناميكية
        $messageIdeas = [
            "اكتشف مجموعتنا المميزة من " . implode(' و ', array_slice($categoryNames, 0, 2)) . " بأسعار لا تقبل المنافسة!",
            "عروض حصرية على منتجات مختارة، لفترة محدودة فقط.",
        ];
        if (count($categoryNames) > 0) {
            $messageIdeas[] = "خصم خاص عند شرائك من فئة " . $categoryNames[0] . " اليوم!";
        }

        return [
            'filters_summary'             => $filtersSummary,
            'target_audience_suggestions' => $targetAudience,
            'marketing_objectives'        => [
                "زيادة الوعي بالعلامة التجارية ضمن الفئات: " . implode('، ', $categoryNames),
                "زيادة المبيعات بنسبة 20% خلال الشهر القادم.",
                "تعزيز ولاء العملاء من خلال عروض حصرية.",
            ],
            'channels'                    => [
                "وسائل التواصل الاجتماعي (فيسبوك، انستغرام، تيك توك)",
                // "البريد الإلكتروني التسويقي",
                // "الإعلانات المدفوعة (Google Ads، إعلانات وسائل التواصل)"
            ],
            'message_ideas'               => $messageIdeas,
            'additional_notes'            => "إجمالي المنتجات المختارة: {$totalProducts} منتج. يمكن التركيز على المنتجات الأعلى سعراً لزيادة هامش الربح.",
        ];
    }

    public function jsonProducts(Request $request)
    {
        $query = Product::query();
        $this->applyFilters($query, $request); // <-- استخدام الدالة الموحّدة

        $products = $query
            ->select(['id', 'name', 'weight', 'price', 'symbol', 'image_path', 'category_id'])
            ->with(['category:id,name'])
            ->get();

        $formatted = $products->map(function ($product) {
            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'weight'        => $product->weight,
                'price'         => $product->price,
                'currency'      => $product->symbol,
                'image'         => $product->image_path,
                'category_id'   => $product->category_id,
                'category_name' => optional($product->category)->name,
            ];
        });

        $grouped = $formatted->groupBy('category_name');

        return response()->json([
            'count'       => $formatted->count(),
            'by_category' => $grouped,
            'products'    => $formatted,
        ]);
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

    public function importTodayInvoices()
    {
        try {
            $files = request()->file('files');

            if (! $files || count($files) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم اختيار أي ملفات',
                ], 400);
            }

            $importedFiles = 0;
            $failedFiles   = 0;
            $errors        = [];

            foreach ($files as $file) {
                try {
                    DB::beginTransaction();

                    Excel::import(new DailyProductsImport, $file);

                    DB::commit();
                    $importedFiles++;

                } catch (\Exception $e) {
                    DB::rollBack();

                    $errorMessage = 'فشل استيراد ملف: ' . $file->getClientOriginalName();
                    Log::error($errorMessage, ['error' => $e->getMessage()]);
                    $errors[] = $errorMessage;
                    $failedFiles++;
                }
            }

            // بناء رسالة النتيجة
            $message = "تم استيراد {$importedFiles} ملف(ملفات) بنجاح";

            if ($failedFiles > 0) {
                $message .= "، وفشل {$failedFiles} ملف(ملفات)";
            }

            $response = [
                'success'        => true,
                'message'        => $message,
                'imported_count' => $importedFiles,
                'failed_count'   => $failedFiles,
            ];

            if (count($errors) > 0) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error('Import error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء استيراد الملفات: ' . $e->getMessage(),
            ], 500);
        }
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

        $request->validate([
            'images.*' => 'required|image',
        ]);

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
            } else {
                $results[] = [
                    'id'     => uniqid(),
                    'name'   => $originalName,
                    'image'  => asset('public/storage/tmp_products/' . $tmpName),
                    'tmp'    => $tmpName,
                    'status' => 'not_found',
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
        $ids   = $request->input('ids', []);
        $index = $request->input('index', 0);
        $field = $request->input('search_field', 'id');

        $ids = json_decode($ids, true) ?? [];

        if (empty($ids)) {
            return response()->json(['error' => 'لم يتم تحديد أي منتجات'], 400);
        }

        // قائمة بيضاء للحقول المسموح بها منعاً لـ SQL injection
        $allowedFields = ['id', 'barcode']; // أضف أي عمود آخر تريد البحث به
        if (!in_array($field, $allowedFields)) {
            $field = 'id';
        }
        // إذا كان ids نصاً مفصولاً بفواصل، حوله إلى مصفوفة
        if (is_string($ids)) {
            $ids = explode(',', $ids);
            $ids = array_filter($ids); // إزالة القيم الفارغة
        }

        $products = Product::whereIn($field, $ids)
            ->whereNotNull('image_path')
            ->with('category')
            ->get();

        return view('design.' . $request->input('design_type'), compact(['products', 'index']))->render();
    }

}
