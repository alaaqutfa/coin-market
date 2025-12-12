<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DailySale;
use App\Models\MeatProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DailySalesController extends Controller
{
    /**
     * عرض نموذج إضافة مبيعات
     */
    public function create()
    {
        $products = MeatProduct::where('is_active', true)
            ->select('id', 'name', 'barcode', 'selling_price')
            ->get();

        return view('meat-inventory.daily-sales.create', compact('products'));
    }

    /**
     * حفظ عملية البيع/الإرجاع
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'meat_product_id'  => 'required|exists:meat_products,id',
            'transaction_type' => 'required|in:sale,return',
            'quantity'         => 'required|numeric|min:0.001',
            'sale_price'       => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:500',
            'sale_date'        => 'required|date',
            'image'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            // رفع صورة من ملف
            $image     = $request->file('image');
            $fileName  = 'sale_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('daily_sales', $fileName, 'public');
        }

        // حساب المبلغ الإجمالي
        $totalAmount = $validated['quantity'] * $validated['sale_price'];

        // إنشاء السجل
        $sale = DailySale::create([
            'meat_product_id'  => $validated['meat_product_id'],
            'sale_date'        => $validated['sale_date'],
            'sale_price'       => $validated['sale_price'],
            'return_price'     => $validated['transaction_type'] === 'return' ? $totalAmount : 0,
            'transaction_type' => $validated['transaction_type'],
            'quantity'         => $validated['quantity'],
            'total_amount'     => $totalAmount,
            'image'            => $imagePath,
            'notes'            => $validated['notes'],
            'transaction_time' => now(),
        ]);

        // تحديث المخزون
        $product = MeatProduct::find($validated['meat_product_id']);
        if ($validated['transaction_type'] === 'sale') {
            $product->current_stock -= $validated['quantity'];
        } else {
            $product->current_stock += $validated['quantity'];
        }
        $product->save();

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'تم حفظ العملية بنجاح',
                'data'    => [
                    'sale'      => $sale,
                    'image_url' => $imagePath ? Storage::url($imagePath) : null,
                ],
            ], 201);
        }

        return redirect()->route('meat-inventory.daily-sales.create')
            ->with('success', 'تم حفظ العملية بنجاح');
    }

    /**
     * تقرير المبيعات حسب التاريخ
     */
    public function report(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate   = $request->input('end_date', date('Y-m-d'));

        // حساب الإحصائيات
        $stats = DailySale::netSales($startDate, $endDate);

        // الحصول على التفاصيل
        $sales = DailySale::with('meatProduct')
            ->dateRange($startDate, $endDate)
            ->orderBy('sale_date', 'desc')
            ->orderBy('transaction_time', 'desc')
            ->paginate(50);

        return view('meat-inventory.daily-sales.report', compact('sales', 'stats', 'startDate', 'endDate'));
    }

    /**
     * تقرير الملخص اليومي
     */
    public function dailySummary(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));

        $summary = DailySale::whereDate('sale_date', $date)
            ->selectRaw('
                transaction_type,
                meat_product_id,
                SUM(quantity) as total_quantity,
                SUM(total_amount) as total_amount,
                COUNT(*) as transaction_count
            ')
            ->with('meatProduct')
            ->groupBy('transaction_type', 'meat_product_id')
            ->get();

        $netAmount = DailySale::netSales($date, $date);

        return view('meat-inventory.daily-sales.daily-summary', compact('summary', 'netAmount', 'date'));
    }
}
