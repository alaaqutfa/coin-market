<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeatProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MeatProductController extends Controller
{
    public function index()
    {
        $products = MeatProduct::where('is_active', true)->get();
        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|max:50|unique:meat_products,barcode,',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'      => 'nullable|string',
            'current_stock'    => 'required|numeric|min:0',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'waste_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data = $request->all();

        // رفع الصورة إذا وجدت
        if ($request->hasFile('image')) {
            $imagePath     = $request->file('image')->store('meat-products', 'public');
            $data['image'] = $imagePath;
        }

        $product = MeatProduct::create($data);

        return response()->json([
            'message' => 'تم إنشاء المنتج بنجاح',
            'product' => $product,
        ], 201);
    }

    public function show(MeatProduct $meatProduct)
    {
        return response()->json($meatProduct);
    }

    public function update(Request $request, MeatProduct $meatProduct)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'barcode'          => 'nullable|string|max:50|unique:meat_products,barcode,' . $meatProduct->id,
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description'      => 'nullable|string',
            'current_stock'    => 'required|numeric|min:0',
            'cost_price'       => 'required|numeric|min:0',
            'selling_price'    => 'required|numeric|min:0',
            'waste_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $data = $request->all();

        // تحديث الصورة إذا وجدت
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة إذا كانت موجودة
            if ($meatProduct->image && Storage::disk('public')->exists($meatProduct->image)) {
                Storage::disk('public')->delete($meatProduct->image);
            }

            $imagePath     = $request->file('image')->store('meat-products', 'public');
            $data['image'] = $imagePath;
        }

        $meatProduct->update($data);

        return response()->json([
            'message' => 'تم تحديث المنتج بنجاح',
            'product' => $meatProduct,
        ]);
    }

    public function destroy(MeatProduct $meatProduct)
    {
        // حذف الصورة إذا كانت موجودة
        if ($meatProduct->image && Storage::disk('public')->exists($meatProduct->image)) {
            Storage::disk('public')->delete($meatProduct->image);
        }

        $meatProduct->delete();

        return response()->json([
            'message' => 'تم حذف المنتج بنجاح',
        ]);
    }
}
