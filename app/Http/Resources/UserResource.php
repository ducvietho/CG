<?php


namespace App\Http\Resources;

use App\Models\Notification;
use App\Models\NurseProfile;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        $noti = Notification::where('user_to',$this->id)->where('unwatch',0)->pluck('id')->toArray();
        $rate = NurseProfile::select('rate')->where('user_login',$this->id)->first();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'address_detail' => ($this->address_detail != null) ? $this->address_detail : '',
            'avatar' => $this->avatar,
            'phone' => $this->phone,
            'type' => $this->type,
            'gender' => $this->gender,
            'type_account' => $this->type_account,
            'birthday' => $this->birthday,
            'setting_care' => $this->setting_care,
            'rate' => ($rate != null)? round($rate->rate,1) : 0,
            'is_register' => $this->is_register,
            'city' => ($this->city != null) ? new CityResource($this->city) : new \stdClass(),
            'district' => ($this->district != null) ? new DistrictResource($this->district) : new \stdClass(),
            'notification' => count($noti),
            'role'=>$this->role
        ];
    }
}