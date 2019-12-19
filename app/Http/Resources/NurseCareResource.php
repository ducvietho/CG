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
use App\Models\NurseProfile;
use App\Models\PatientInterest;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class NurseCareResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_request' => $this->id,
            'nurse' =>  new NurseHomeResource(NurseProfile::where('user_login',$this->user_nurse)->first())
        ];
    }

}