<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(20);
        return response()->json($products);
    }

    public function home()
    {
        $products = Product::paginate(20);
        return view('home', compact('products'));
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
}
