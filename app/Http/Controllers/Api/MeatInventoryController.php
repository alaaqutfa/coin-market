<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeatInventoryMovement;
use App\Models\MeatProduct;
use Illuminate\Http\Request;

class MeatInventoryController extends Controller
{
    public function index()
    {
        $movements = MeatInventoryMovement::with('product')->latest()->get();
        return response()->json($movements);
    }

    // تسجيل خروج للبيع (من المستودع للملحمه)
    public function recordSale(Request $request)
    {
        $request->validate([
            'meat_product_id' => 'required|exists:meat_products,id',
            'quantity'        => 'required|numeric|min:0.1',
            'unit_price'      => 'required|numeric|min:0',
            'movement_date'   => 'required|date',
            'notes'           => 'nullable|string',
        ]);

        $product = MeatProduct::find($request->meat_product_id);

        // التحقق من توفر المخزون
        if ($product->current_stock < $request->quantity) {
            return response()->json([
                'error' => 'الكمية غير متوفرة. المتوفر: ' . $product->current_stock . ' كغ',
            ], 400);
        }

        $movement = MeatInventoryMovement::create([
            'meat_product_id' => $request->meat_product_id,
            'movement_type'   => 'out',
            'quantity'        => $request->quantity,
            'unit_price'      => $request->unit_price,
            'total_price'     => $request->quantity * $request->unit_price,
            'movement_date'   => $request->movement_date,
            'notes'           => $request->notes ?? 'بيع - خروج للملحمه',
        ]);

        return response()->json([
            'message'  => 'تم تسجيل خروج البضاعة بنجاح',
            'movement' => $movement->load('product'),
        ], 201);
    }

    // تسجيل إرجاع (من الملحمه للمستودع)
    public function recordReturn(Request $request)
    {
        $request->validate([
            'meat_product_id' => 'required|exists:meat_products,id',
            'quantity'        => 'required|numeric|min:0.1',
            'movement_date'   => 'required|date',
            'notes'           => 'nullable|string',
        ]);

        $movement = MeatInventoryMovement::create([
            'meat_product_id' => $request->meat_product_id,
            'movement_type'   => 'return',
            'quantity'        => $request->quantity,
            'unit_price'      => 0, // الإرجاع ليس بيع
            'total_price'     => 0,
            'movement_date'   => $request->movement_date,
            'notes'           => $request->notes ?? 'إرجاع باقي للمستودع',
        ]);

        return response()->json([
            'message'  => 'تم تسجيل الإرجاع بنجاح',
            'movement' => $movement->load('product'),
        ], 201);
    }

    // تسجيل هدر
    public function recordWaste(Request $request)
    {
        $request->validate([
            'meat_product_id' => 'required|exists:meat_products,id',
            'quantity'        => 'required|numeric|min:0.1',
            'movement_date'   => 'required|date',
            'notes'           => 'nullable|string',
        ]);

        $product = MeatProduct::find($request->meat_product_id);

        $movement = MeatInventoryMovement::create([
            'meat_product_id' => $request->meat_product_id,
            'movement_type'   => 'waste',
            'quantity'        => $request->quantity,
            'unit_price'      => $product->cost_price, // تكلفة الهدر
            'total_price'     => $request->quantity * $product->cost_price,
            'movement_date'   => $request->movement_date,
            'notes'           => $request->notes ?? 'هدر',
        ]);

        return response()->json([
            'message'  => 'تم تسجيل الهدر بنجاح',
            'movement' => $movement->load('product'),
        ], 201);
    }

    // التقارير اليومية
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? now()->format('Y-m-d');

        $sales = MeatInventoryMovement::with('product')
            ->where('movement_type', 'out')
            ->whereDate('movement_date', $date)
            ->get();

        $returns = MeatInventoryMovement::with('product')
            ->where('movement_type', 'return')
            ->whereDate('movement_date', $date)
            ->get();

        $waste = MeatInventoryMovement::with('product')
            ->where('movement_type', 'waste')
            ->whereDate('movement_date', $date)
            ->get();

        $totalSales          = $sales->sum('total_price');
        $totalWeightSold     = $sales->sum('quantity');
        $totalWeightReturned = $returns->sum('quantity');
        $totalWaste          = $waste->sum('quantity');

        // حساب المبيع الفعلي (الخروج - الإرجاع)
        $actualSoldWeight = $totalWeightSold - $totalWeightReturned;

        return response()->json([
            'date'               => $date,
            'total_sales'        => $totalSales,
            'sold_weight'        => $totalWeightSold,
            'returned_weight'    => $totalWeightReturned,
            'actual_sold_weight' => $actualSoldWeight,
            'waste_weight'       => $totalWaste,
            'sales_count'        => $sales->count(),
            'sales'              => $sales,
            'returns'            => $returns,
            'waste'              => $waste,
        ]);
    }
}
