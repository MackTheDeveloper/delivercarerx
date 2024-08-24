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
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Auth;
use Request;


class OrdersNumbersExport implements FromArray, WithHeadings, WithStyles, WithStartRow
{
    protected $isGlobal;

    function __construct($isGlobal)
    {
        $this->isGlobal = $isGlobal;
    }

    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    public function styles(Worksheet $sheet)
    {
        return [

        ];
    }

    public function headings(): array
    {
        return [
            'delivercare_order_number',
            'newleaf_order_number',
            'patient_name',
            'pharmacy_id'
        ];
    }


    /**
     * @return \Illuminate\Support\Collection
     */
    // public function collection()
    public function array(): array
    {
        if ($this->isGlobal == 'global') {
            $query = RefillOrder::select('refill_orders.order_number', 'refill_orders.patient_name', 'refill_orders.pharmacy_id' )->whereNull('refill_orders.newleaf_order_number')->whereNull('deleted_at');
            $query = $query->distinct()->get();

            $data = [];
            foreach ($query as $key => $value) {
                $data[] = [(string)$value['order_number'], null, $value['patient_name'], $value['pharmacy_id']];
            }
            return $data;
        } else {
            if (Auth::user()->role_id == '1') {
                $query = RefillOrder::select('refill_orders.order_number', 'refill_orders.patient_name', 'refill_orders.pharmacy_id')->whereNull('refill_orders.newleaf_order_number')->whereNull('deleted_at');
                $query = $query->distinct()->get();
            }
            $data = [];
            foreach ($query as $key => $value) {
                $data[] = [(string)$value['order_number'], null, $value['patient_name'], $value['pharmacy_id']];
            }
            return $data;
        }

    }


}
