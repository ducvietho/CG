<?php

namespace App\Http\Controllers;

use App\Imports\CityImport;
use App\Traits\ApiResponser;
use App\Imports\DistrictImport;
use Maatwebsite\Excel\Facades\Excel;

class LocationController extends Controller
{
    use ApiResponser;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    //Import city 
    public function import() 
    {
        Excel::import(new CityImport, '../public/location_korean.xlsx');
        return $this->successResponseMessage(new \stdClass, 200, "Import success");
    }
    //Import district 
    public function importDistrict() 
    {
        Excel::import(new DistrictImport, '../public/district.xlsx');
        return $this->successResponseMessage(new \stdClass, 200, "Import success");
    }
}
