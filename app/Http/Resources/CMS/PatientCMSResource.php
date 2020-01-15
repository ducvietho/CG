<?php


namespace App\Http\Resources\CMS;


use App\Http\Resources\CityResource;
use App\Http\Resources\DistrictResource;
use App\Models\City;
use App\Models\District;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientCMSResource extends JsonResource
{
    public function toArray($request)
    {
        $city_code = substr($this->code_add, 0, 2);
        $city_name = City::where('code', $city_code)->first();
        $district = District::where('code', $this->code_add)->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'avatar' => $this->avatar,
            'city' => ($city_name == null) ? new \stdClass() : new CityResource($city_name),
            'district' => ($district == null) ? new \stdClass() : new DistrictResource($district),
            'birthday' => $this->birthday,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'number_like' => $this->getLikes()->count()

        ];
    }
}