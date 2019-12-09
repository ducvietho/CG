<?php

namespace App\Imports;

use App\Models\City;
use Maatwebsite\Excel\Concerns\ToModel;

class CityImport implements ToModel
{
    /**
     * @param array $row
     *
     * @return User|null
     */
    public function model(array $row)
    {
        return new City([
           'code'     => $row[0],
           'original_name'    => $row[1], 
           'show_name' => $row[2],
        ]);
    }
}