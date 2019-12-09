<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\District;
use App\Imports\CityImport;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Imports\DistrictImport;
use App\Http\Resources\CityResource;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\DistrictResource;

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

    //Get list city
    public function getListCity(Request $request){
        $key = $request->key;
        $data = City::where('original_name','like','%'.$key.'%')->orWhere('show_name','like','%'.$key.'%')->get();
        return $this->successResponseMessage(CityResource::collection($data),200,'Get city success');
    }

    //Get list district
    public function getListDistrict(Request $request){
        $key = $request->key;
        $city_code = $request->city_code;
        $data = District::where('city_id',$city_code)
        ->where(function ($q) use ($key) {
            $q->where('original_name','like','%'.$key.'%')->orWhere('show_name','like','%'.$key.'%');
        })->get();
        return $this->successResponseMessage(DistrictResource::collection($data),200,'Get district success');
    }
}
