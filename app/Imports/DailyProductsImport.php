<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\ProductBarcodeLog;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;


class DailyProductsImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // تجميع البيانات حسب الباركود (أخذ آخر قيمة لكل باركود)
        $groupedData = [];

        foreach ($rows as $row) {
            $barcode = $this->getBarcodeFromRow($row->toArray());

            if (!$barcode) {
                continue;
            }

            // تخزين بيانات الباركود (البيانات اللاحقة ستتجاوز السابقة)
            $groupedData[$barcode] = [
                'barcode' => $barcode,
                'name' => $this->getNameFromRow($row->toArray()),
                'price' => $this->getPriceFromRow($row->toArray()),
                'quantity' => $this->getQuantityFromRow($row->toArray()),
            ];
        }

        // الآن معالجة كل باركود مرة واحدة فقط
        foreach ($groupedData as $data) {
            $this->processProduct($data);
        }
    }

    /**
     * معالجة منتج واحد
     */
    private function processProduct(array $data): void
    {
        $barcode = $data['barcode'];
        $name = $data['name'];
        $price = $data['price'];
        $quantity = $data['quantity'];

        // البحث عن المنتج الموجود
        $existingProduct = Product::where('barcode', $barcode)->first();

        // تسجيل الباركود
        $this->logBarcode($barcode, $existingProduct);

        if ($existingProduct) {
            $existingProduct->update([
                'price'    => $price > 0 ? $price : $existingProduct->price,
                'quantity' => $quantity,
            ]);
        } else {
            // إنشاء منتج جديد
            Product::create([
                'barcode'  => $barcode,
                'name'     => $name,
                'price'    => $price,
                'quantity' => $quantity,
                'weight'   => 0,
            ]);
        }
    }

    /**
     * الحصول على الباركود من الصف
     */
    private function getBarcodeFromRow(array $row): ?string
    {
        $possibleColumns = ['barcod', 'code', 'barcode', 'باركود', 'باركود المنتج'];

        foreach ($possibleColumns as $column) {
            if (isset($row[$column]) && !empty($row[$column])) {
                return (string) $row[$column];
            }
        }

        return null;
    }

    /**
     * الحصول على الاسم من الصف
     */
    private function getNameFromRow(array $row): string
    {
        $possibleColumns = ['bardes', 'description', 'name', 'product_name', 'الاسم', 'اسم المنتج'];

        foreach ($possibleColumns as $column) {
            if (isset($row[$column]) && !empty($row[$column])) {
                return (string) $row[$column];
            }
        }

        return '';
    }

    /**
     * الحصول على السعر من الصف
     */
    private function getPriceFromRow(array $row): float
    {
        $possibleColumns = ['retailr', 'price', 'retail_price', 'السعر', 'سعر البيع'];

        foreach ($possibleColumns as $column) {
            if (isset($row[$column]) && !empty($row[$column])) {
                return (float) $row[$column];
            }
        }

        return 0.0;
    }

    /**
     * الحصول على الكمية من الصف
     */
    private function getQuantityFromRow(array $row): int
    {
        $possibleColumns = ['qty', 'quantity', 'الكمية', 'عدد الوحدات'];

        foreach ($possibleColumns as $column) {
            if (isset($row[$column]) && !empty($row[$column])) {
                return (int) $row[$column];
            }
        }

        return 0;
    }

    /**
     * تسجيل الباركود في سجل المنتجات
     */
    private function logBarcode(string $barcode, ?Product $existingProduct): void
    {
        try {
            ProductBarcodeLog::create([
                'barcode' => $barcode,
                'exists'  => !is_null($existingProduct),
                'source'  => 'importTodayInvoices',
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to log barcode: ' . $barcode, ['error' => $e->getMessage()]);
        }
    }
}
