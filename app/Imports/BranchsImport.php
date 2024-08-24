<?php

namespace App\Imports;

use App\Models\Branch;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Auth;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class BranchsImport implements ToCollection
{


private $common = array();
    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        $data = [];
        foreach ($collection as $row)
        {
            if($row[0] == "FacilityId" || $row[1] == "IsActive" || $row[2] == "Name" || $row[3] == "Hospice(Client)" || $row[4] == "Pharmacy" || $row[5] == "Code")
            {
                continue;
            }
            $data[] = array($row[0],$row[1],$row[2],$row[3],$row[4],$row[5]);
        }
        $this->common = $data;
    }

    public function getCommon(): array
    {
        return $this->common;
    }
}
