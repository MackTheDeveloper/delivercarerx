<?php

namespace App\Exports;

// use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\RefillOrder;
use App\Models\Shipping;
use App\Models\Hospice;
use App\Repository\ActivityRepository;
use App\Repository\HospiceRepository;
use App\Repository\UserRepository;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Auth;
use Request;

class OrdersExport implements FromArray, WithHeadings, WithStyles
{

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],

            // Styling a specific cell by coordinate.
            // 'B2' => ['font' => ['italic' => true]],

            // Styling an entire column.
            // 'C'  => ['font' => ['size' => 16]],
        ];
    }

    public function headings(): array
    {
        return [
            'PATIENTS',
            'DATE ORDERED',
            'ORDER ID',
            'STATUS',
            'SHIPPED BY',
            'TRACKING NO',
            'HOSPICE',
        ];
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    public function array(): array
    {
        $req = Request::all();
        $search = $req['search'];
        if (Auth::user()->role_id == '1') {
            $total = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at')->first();
            $query = RefillOrder::select('refill_orders.*')->whereNull('deleted_at');
            $filteredq = RefillOrder::selectRaw('count(*) as total')->whereNull('deleted_at');
            $totalfiltered = $total->total;
            if ($search != '') {
                $query->where(function ($query2) use ($search) {
                    $query2->where('patient_name', 'like', '%' . $search . '%');
                });
                $filteredq->where(function ($query2) use ($search) {
                    $query2->where('patient_name', 'like', '%' . $search . '%');
                });
                $filteredq = $filteredq->selectRaw('count(*) as total')->first();
                $totalfiltered = $filteredq->total;
            }
            if (($req['startDate'] != "" || $req['endDate'] != "") && $req['startDate'] != $req['endDate']) {
                $startDate = date('Y-m-d', strtotime($req['startDate']));
                $endDate = date('Y-m-d', strtotime($req['endDate']));
                $query->whereBetween('created_at', [$startDate, $endDate]);
                $filteredq->whereBetween('created_at', [$startDate, $endDate]);
                $filteredq = $filteredq->selectRaw('count(*) as total')->first();
                $totalfiltered = $filteredq->total;
            }
            if ($req['status'] != '') {
                if ($req['status'] == 'Pending') {
                    $query->where('status', '1');
                    $filteredq->where('status', '1');
                } elseif ($req['status'] == 'In Progress') {
                    $query->where('status', '2');
                    $filteredq->where('status', '2');
                } elseif ($req['status'] == 'Shipped') {
                    $query->where('status', '3');
                    $filteredq->where('status', '3');
                }
                $filteredq = $filteredq->selectRaw('count(*) as total')->first();
                $totalfiltered = $filteredq->total;
            }

            if ($req['branch_id'] != '') {
                $query->where('hospice_branch_id', $req['branch_id']);
                $filteredq->where('hospice_branch_id', $req['branch_id']);
                $filteredq = $filteredq->selectRaw('count(*) as total')->first();
                $totalfiltered = $filteredq->total;
            }
            $query = $query->distinct()->get();
        }
        $data = [];
        foreach ($query as $key => $value) {
            if ($value['status'] == 1) {
                $status = 'PENDING';
            } else if ($value['status'] == 2) {
                $status = 'IN PROGRESS';
            } else if ($value['status'] == 3) {
                $status = 'SHIPPED';
            } else {
                $status = '';
            }
            if (!empty($value['hospice_id'])) {
                $hospiceData = Hospice::where('id',$value['hospice_id'])->first();
                if (!empty($hospiceData)) {
                    $hospiceName = $hospiceData->code;
                } else {
                    $hospiceName = '';
                }
            }
            $tracking_number = $value['tracking_number'] ?? '-';
            $shippingLogoVal = Shipping::find($value['shipped_by']);
            $logoHtml = '';
            if ($shippingLogoVal) {
                $logoHtml = $shippingLogoVal->name;
            } else {
                $logoHtml = '-';
            }
            $data[] = [$value['patient_name'], getFormatedDate($value['created_at']), $value['order_number'], $status, $logoHtml ?? '', $tracking_number, $hospiceName ?? ''];
        }
        return $data;
    }
}
