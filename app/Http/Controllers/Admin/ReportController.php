<?php
namespace App\Http\Controllers\Admin;

use App\Exports\SalesReportExport;
use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|in:daily,monthly',
        ]);

        $export = new SalesReportExport($request->input('from_date'), $request->input('to_date'), $request->input('type'));
        return Excel::download($export, 'sales_report_' . date('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|in:daily,monthly',
        ]);

        if ($request->input('type') == 'daily') {
            $orders = Order::whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')])
                ->with('customer')
                ->orderBy('created_at')
                ->get();
            $data = ['orders' => $orders, 'from' => $request->input('from_date'), 'to' => $request->input('to_date')];
            $pdf  = Pdf::loadView('admin.reports.daily-pdf', $data);
        } else {
            $monthly = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total_orders, SUM(total) as total_amount')
                ->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
            $data = ['monthly' => $monthly, 'from' => $request->input('from_date'), 'to' => $request->input('to_date')];
            $pdf  = Pdf::loadView('admin.reports.monthly-pdf', $data);
        }

        return $pdf->download('sales_report.pdf');
    }

    public function export(Request $request)
    {
        $request->validate([
            'from_date' => 'required|date',
            'to_date'   => 'required|date|after_or_equal:from_date',
            'type'      => 'required|in:daily,monthly',
            'format'    => 'required|in:excel,pdf',
        ]);

        if ($request->input('format') == 'excel') {
            $export = new SalesReportExport($request->input('from_date'), $request->input('to_date'), $request->input('type'));
            return Excel::download($export, 'sales_report.xlsx');
        } else {
            if ($request->input('type') == 'daily') {
                $orders = Order::whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')])->with('customer')->get();
                $pdf    = Pdf::loadView('admin.reports.daily-pdf', ['orders' => $orders, 'from' => $request->input('from_date'), 'to' => $request->input('to_date')]);
            } else {
                $monthly = Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total_orders, SUM(total) as total_amount')
                    ->whereBetween('created_at', [$request->input('from_date'), $request->input('to_date')])
                    ->groupBy('month')->get();
                $pdf = Pdf::loadView('admin.reports.monthly-pdf', ['monthly' => $monthly, 'from' => $request->input('from_date'), 'to' => $request->input('to_date')]);
            }
            return $pdf->download('sales_report.pdf');
        }
    }
}
