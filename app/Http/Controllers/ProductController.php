<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductBarcodeLog;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::whereNotNull('image_path')->latest()->paginate(50);
        return response()->json($products);
    }

    public function list()
    {
        $products = Product::latest()->paginate(50);
        return view('products.view', compact('products'));
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
        if ($request->price) {
            $query->where('price', $request->price);
        }

        // الفلترة حسب الوزن
        if ($request->weight) {
            $query->where('weight', $request->weight);
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

        // الترتيب من الأحدث إلى الأقدم
        $products = $query->latest()->paginate(50);
        $products->withPath(url('/products'));

        return view('products.partials.products-table', compact('products'))->render();
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcode' => 'required|unique:products',
            'name'    => 'required|max:255',
            'price'   => 'required|numeric|min:0',
            'image'   => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->only(['barcode', 'name', 'description', 'price', 'weight', 'quantity']);

        // معالجة الصورة بدون Intervention
        if ($request->hasFile('image')) {
            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product = Product::create($productData);

        return response()->json($product, 201);
    }

    public function bulkStore(Request $request)
    {
        $products = $request->input('products', []);

        // استخرج كل الباركودات المرسلة
        $barcodes = collect($products)->pluck('barcode')->filter()->unique();

        // جيب الباركودات الموجودة أصلاً
        $existingBarcodes = Product::whereIn('barcode', $barcodes)->pluck('barcode')->toArray();

        $insertData = [];
        foreach ($products as $p) {
            if (! empty($p['barcode']) && ! empty($p['name'])) {
                $exists = in_array($p['barcode'], $existingBarcodes);

                // سجل اللوج
                ProductBarcodeLog::create([
                    'barcode' => $p['barcode'],
                    'exists'  => ! $exists,
                    'source'  => 'bulkStore',
                ]);

                if (! $exists) {
                    $insertData[] = [
                        'barcode'    => $p['barcode'],
                        'name'       => $p['name'],
                        'price'      => $p['price'] ?? 0,
                        'weight'     => $p['weight'] ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
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
            'skipped'  => count($existingBarcodes), // المنتجات اللي تم تخطيها
        ]);
    }

    // public function getMissingProducts()
    // {
    //     // الباركودات الموجودة فعلياً في جدول المنتجات
    //     $existing = Product::pluck('barcode')->toArray();

    //     // جيب الباركودات المفقودة مباشرة من اللوج وفلترهم حسب created_at
    //     $missing = ProductBarcodeLog::whereNotIn('barcode', $existing)
    //         ->orderBy('created_at', 'asc')
    //         ->pluck('barcode')
    //         ->toArray();

    //     return response()->json($missing);
    // }
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
            'barcode' => 'sometimes|unique:products,barcode,' . $id,
            'name'    => 'sometimes|max:255',
            'price'   => 'sometimes|numeric|min:0',
            'image'   => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->only(['barcode', 'name', 'description', 'price', 'weight', 'quantity']);

        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($product->image_path && Storage::exists($product->image_path)) {
                Storage::delete($product->image_path);
            }

            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product->update($productData);

        return response()->json($product);
    }

    public function updateByBarcode(Request $request, $barcode)
    {
        $product = Product::where('barcode', $barcode)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'barcode' => 'sometimes|unique:products,barcode,' . $product->id,
            'name'    => 'sometimes|max:255',
            'price'   => 'sometimes|numeric|min:0',
            'image'   => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $productData = $request->only(['barcode', 'name', 'description', 'price', 'weight', 'quantity']);

        if ($request->hasFile('image')) {
            if ($product->image_path && Storage::exists($product->image_path)) {
                Storage::delete($product->image_path);
            }
            $imagePath                 = $this->storeImage($request->file('image'), $request->barcode);
            $productData['image_path'] = $imagePath;
        }

        $product->update($productData);

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
        $files   = $request->file('images', []);
        $results = [];

        if (! is_array($files) || count($files) === 0) {
            return response()->json([]);
        }

        foreach ($files as $file) {
            if (! $file->isValid()) {
                $results[] = [
                    'status'   => 'error',
                    'message'  => 'invalid_file',
                    'original' => $file->getClientOriginalName(),
                ];
                continue;
            }

            // نفترض أن اسم الملف بدون الامتداد هو الباركود
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

            // البحث عبر barcode (كما في كودك)
            $product = \App\Models\Product::where('barcode', $originalName)->first();

            // اسم مؤقت آمن
            $tmpName = uniqid() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
            $tmpPath = 'tmp_products/' . $tmpName;

            // خزن في disk 'public' داخل storage/app/public/tmp_products
            Storage::disk('public')->putFileAs('tmp_products', $file, $tmpName);

            if ($product) {
                $results[] = [
                    'id'     => $product->id,
                    'name'   => $product->name,
                    // المسار الذي يمكن عرضه عبر asset()-> storage symlink يجب أن يكون storage/...
                    'image'  => asset('storage/' . $tmpPath),
                    'tmp'    => $tmpName,
                    'status' => 'matched',
                ];
            } else {
                $results[] = [
                    'name'   => $originalName,
                    'image'  => asset('storage/' . $tmpPath),
                    'tmp'    => $tmpName,
                    'status' => 'unmatched',
                ];
            }
        }

        return response()->json($results);
    }

    public function saveImages(Request $request)
    {
        $items = $request->input('items', []);

        if (! is_array($items) || count($items) === 0) {
            return response()->json(['status' => 'no_items']);
        }

        foreach ($items as $item) {
            if (! isset($item['id']) || ! isset($item['tmp'])) {
                // تخطي العناصر غير المكتملة
                continue;
            }

            $product = \App\Models\Product::find($item['id']);
            if (! $product) {
                Log::warning("saveImages: product not found for id {$item['id']}");
                continue;
            }

            $tmpName     = $item['tmp'];
            $tmpRelative = 'tmp_products/' . $tmpName;
            $destDir     = 'products';

            // تأكد أن الملف المؤقت موجود
            if (! Storage::disk('public')->exists($tmpRelative)) {
                Log::warning("saveImages: tmp file not found: {$tmpRelative}");
                continue;
            }

            // امتداد الملف
            $extension = pathinfo($tmpName, PATHINFO_EXTENSION) ?: 'jpg';

            // اسم آمن يعتمد على اسم المنتج والسعر
            $safeName    = Str::slug($product->name . '-' . $product->price, '_');
            $newName     = $safeName . '.' . $extension;
            $newRelative = $destDir . '/' . $newName;

            // إذا كان هناك ملف قديم نريد حذفه
            if (! empty($product->image_path)) {
                // تأكد من أن image_path مخزن نسبياً مثل "products/xxx.jpg"
                $oldRelative = $product->image_path;
                if (Storage::disk('public')->exists($oldRelative)) {
                    Storage::disk('public')->delete($oldRelative);
                } else {
                    // ربما تخزين قديم يختلف؛ حاول الحذف من 'storage/' prefix
                    if (strpos($oldRelative, 'storage/') === 0) {
                        $maybe = substr($oldRelative, strlen('storage/'));
                        if (Storage::disk('public')->exists($maybe)) {
                            Storage::disk('public')->delete($maybe);
                        }
                    }
                }
            }

            // انقل/إعادة تسمية الملف من tmp_products => products
            Storage::disk('public')->move($tmpRelative, $newRelative);

            // تحديث قاعدة البيانات (احفظ المسار النسبي ضمن disk public)
            $product->image_path = $newRelative;
            $product->save();
        }

        // امسح tmp_products المتبقي إن أردت (مثال)
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
