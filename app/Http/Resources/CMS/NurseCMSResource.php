<?php


namespace App\Http\Resources\CMS;


use App\Http\Resources\CityResource;
use App\Http\Resources\DistrictResource;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Resources\Json\JsonResource;

class NurseCMSResource extends JsonResource
{
    public function toArray($request)
    {
        $listLocation = json_decode($this->code_add);
        $location = $listLocation[0];
        if(isset($request->city_code) && $request->city_code != null){
            foreach ($listLocation as $value){
                if(substr($value,0,2)== $request->city_code){
                    $location = $value;
                    break;
                }
            }
        }
        if (isset($request->district_code) && $request->district_code != null) {
            if (in_array($request->district_code, $listLocation)) {
                $location = $request->district_code;
            }
        }
        $city_code = substr($location, 0, 2);
        $city_name = City::where('code', $city_code)->first();
        $district = District::where('code', $location)->first();

        return [
            'id' => $this->user_login,
            'name' => $this->user->name,
            'birthday' => $this->user->birthday,
            'city' => ($city_name == null) ? new \stdClass() : new CityResource($city_name),
            'district' => ($district == null) ? new \stdClass() : new DistrictResource($district),
            'rate' => round($this->rate, 1),
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'number_like' => $this->getLikes()->count()

        ];
    }
}