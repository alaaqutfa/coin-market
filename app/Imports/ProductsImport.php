<?php
namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        dd($row);
        // التحقق من وجود code
        if (! isset($row['code']) || empty($row['code'])) {
            return null;
        }

        // ابحث عن المنتج
        $product = Product::where('barcode', $row['code'])->first();

        if ($product) {
            // تحديث المنتج الموجود
            $product->update([
                // 'name'     => $row['description'] ?? $product->name,
                'price'    => $row['price'] ?? $product->price,
                'quantity' => $row['qty'] ?? $product->quantity,
                'weight'   => 0,
            ]);

            return null;
        }

        // إنشاء منتج جديد
        return new Product([
            'barcode'  => $row['code'],
            'name'     => $row['description'] ?? '',
            'price'    => $row['price'] ?? 0,
            'quantity' => $row['qty'] ?? 0,
            'weight'   => 0,
        ]);
    }
}
