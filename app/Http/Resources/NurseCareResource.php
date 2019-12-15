<?php
/**
 * Created by PhpStorm.
 * User: ducvietho
 * Date: 12/15/2019
 * Time: 11:30
 */

namespace App\Http\Resources;


use App\Models\City;
use App\Models\District;
use App\Models\PatientInterest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NurseCareResource extends JsonResource
{
    public function toArray($request)
    {
        $city_code = substr($this->nurse->code_add, 0, 2);
        $city_name = City::where('code', $city_code)->first();
        $district = District::where('code', $this->nurse->code_add)->first();
        return [
            'id' => $this->nurse->id,
            'name' => $this->nurse->user->name,
            'avatar' => $this->nurse->user->avatar,
            'age' => $this->age($this->nurse->user->birthday),
            'city' => ($city_name == null) ? new \stdClass() : new CityResource($city_name),
            'district' => ($district == null) ? new \stdClass() : new DistrictResource($district),
            'setting_care' => $this->nurse->user->setting_care,
            'is_interest' => $this->is_interest($this->nurse->id, Auth::id()),
            'user_caring' => new PatientResource($this->patient),

        ];
    }

    private function age($year)
    {
        return date("Y") - $year;
    }

    private function is_interest($id_nures, $id_login)
    {
        $record = PatientInterest::where('user_nurse', $id_nures)->where('user_login', $id_login)->first();
        return ($record == null) ? 0 : 1;
    }
}