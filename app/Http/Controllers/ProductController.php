<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
// use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\Browsershot\Browsershot;

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

        // مع صورة
        if ($request->no_image) {
            $query->whereNull('image_path');
        }

        // فلترة التاريخ حسب النطاق المخصص (من تاريخ - إلى تاريخ)
        if ($request->date_from && $request->date_to) {
            $query->whereBetween('created_at', [
                $request->date_from . ' 00:00:00',
                $request->date_to . ' 23:59:5 9',
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

        // ProductBarcodeLog::createTemporaryNotification(
        //     $product->name . ' ' . $product->price . '/' . $product->weight . ' added successfolly',
        //     $request->note,
        //     1
        // );

        return response()->json($product, 201);
    }

    public function trackProductBarcodeLog()
    {

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
            $cleanName = preg_replace('/ - \d+(\.\d+)?\$/', '', $originalName);

            // البحث عن المنتج
            $product = Product::where('name', $cleanName)->first();

            // فقط لو لقى المنتج يكمل
            if ($product) {
                // حفظ مؤقت في مجلد public/tmp_products
                $tmpName = uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('storage/tmp_products'), $tmpName);

                $results[] = [
                    'id'     => $product->id,
                    'name'   => $product->name,
                    'image'  => asset('storage/tmp_products/' . $tmpName),
                    'tmp'    => $tmpName,
                    'status' => 'matched',
                ];
            }
        }

        return response()->json($results);
    }

    public function saveImages(Request $request)
    {
        $items = $request->input('items'); // array of {id, tmp}

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

                    // نقل الملف
                    rename($tmpPath, $newPath);

                    // تحديث المنتج
                    $product->update([
                        'image_path' => 'products/' . $newName,
                    ]);
                }
            }
        }

        return response()->json(['status' => 'success']);
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
