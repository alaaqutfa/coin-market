<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MeatPurchaseInvoice;
use App\Models\MeatPurchaseItem;
use Illuminate\Http\Request;

class MeatPurchaseController extends Controller
{
    public function index()
    {
        $invoices = MeatPurchaseInvoice::with('items.product')->latest()->get();
        return response()->json($invoices);
    }

    public function store(Request $request)
    {

        if ($request->isJson()) {
            $data = $request->json()->all();
            $request->merge($data);
        }

        $request->validate([
            'supplier_name'           => 'nullable|string|max:255',
            'purchase_date'           => 'required|date',
            'notes'                   => 'nullable|string',
            'items'                   => 'required|array|min:1',
            'items.*.meat_product_id' => 'required|exists:meat_products,id',
            'items.*.quantity'        => 'required|numeric|min:0.1',
            'items.*.unit_cost'       => 'required|numeric|min:0',
        ]);

        // إنشاء الفاتورة
        $invoice = MeatPurchaseInvoice::create([
            'invoice_number' => MeatPurchaseInvoice::generateInvoiceNumber(),
            'supplier_name'  => $request->supplier_name,
            'purchase_date'  => $request->purchase_date,
            'notes'          => $request->notes,
            'total_amount'   => 0, // سيتم تحديثه لاحقاً
        ]);

        $totalAmount = 0;

        // إضافة العناصر
        foreach ($request->items as $itemData) {
            $item = MeatPurchaseItem::create([
                'purchase_invoice_id' => $invoice->id,
                'meat_product_id'     => $itemData['meat_product_id'],
                'quantity'            => $itemData['quantity'],
                'unit_cost'           => $itemData['unit_cost'],
            ]);

            $totalAmount += $item->total_cost;
        }

        // تحديث المبلغ الإجمالي
        $invoice->update(['total_amount' => $totalAmount]);

        return response()->json([
            'message' => 'تم إنشاء فاتورة الشراء بنجاح',
            'invoice' => $invoice->load('items.product'),
        ], 201);
    }

    public function show(MeatPurchaseInvoice $meatPurchase)
    {
        return response()->json($meatPurchase->load('items.product'));
    }

    public function update(Request $request, MeatPurchaseInvoice $meatPurchase)
    {
        $request->validate([
            'supplier_name' => 'sometimes|string|max:255',
            'purchase_date' => 'sometimes|date',
            'notes'         => 'nullable|string',
        ]);

        $meatPurchase->update($request->all());

        return response()->json([
            'message' => 'تم تحديث فاتورة الشراء بنجاح',
            'invoice' => $meatPurchase->load('items.product'),
        ]);
    }

    public function destroy(MeatPurchaseInvoice $meatPurchase)
    {
        // تراجع عن تحديث المخزون قبل الحذف
        foreach ($meatPurchase->items as $item) {
            $item->product->updateStock($item->quantity, 'out');
        }

        $meatPurchase->delete();

        return response()->json([
            'message' => 'تم حذف فاتورة الشراء بنجاح',
        ]);
    }
}
