<?php
namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SalesReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $fromDate;
    protected $toDate;
    protected $type; // 'daily' or 'monthly'

    public function __construct($fromDate, $toDate, $type = 'daily')
    {
        $this->fromDate = $fromDate;
        $this->toDate   = $toDate;
        $this->type     = $type;
    }

    public function collection()
    {
        if ($this->type == 'daily') {
            return Order::whereBetween('created_at', [$this->fromDate, $this->toDate])
                ->with('customer')
                ->orderBy('created_at')
                ->get();
        } else {
            // monthly summary: group by month
            return Order::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as total_orders, SUM(total) as total_amount')
                ->whereBetween('created_at', [$this->fromDate, $this->toDate])
                ->groupBy('month')
                ->orderBy('month')
                ->get();
        }
    }

    public function headings(): array
    {
        if ($this->type == 'daily') {
            return ['رقم الطلب', 'العميل', 'الهاتف', 'الإجمالي', 'الحالة', 'التاريخ'];
        } else {
            return ['الشهر', 'عدد الطلبات', 'إجمالي المبيعات'];
        }
    }

    public function map($row): array
    {
        if ($this->type == 'daily') {
            return [
                $row->order_number,
                $row->customer->name,
                $row->customer->phone,
                number_format($row->total, 2),
                $row->status == 'accepted' ? 'مقبول' : ($row->status == 'rejected' ? 'مرفوض' : 'قيد الانتظار'),
                $row->created_at->format('Y-m-d'),
            ];
        } else {
            return [
                $row->month,
                $row->total_orders,
                number_format($row->total_amount, 2),
            ];
        }
    }
}
