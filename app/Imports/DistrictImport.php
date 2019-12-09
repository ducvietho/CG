<?php

namespace App\Imports;

use App\Models\District;
use Maatwebsite\Excel\Concerns\ToModel;

class DistrictImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new District([
           'city_id'     => $row[0],
           'original_name'    => $row[1], 
           'show_name' => $row[2],
           'code' => $row[3],
        ]);
    }
}