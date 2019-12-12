<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'address_detail' => ($this->address_detail != null) ? $this->address_detail : '',
            'avatar' => $this->avatar,
            'type' => $this->type,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'setting_care' => $this->setting_care,
            'rate' => $this->rate,
            'is_register' => $this->is_register,
            'city' => ($this->city != null) ? new CityResource($this->city) : new \stdClass(),
            'district' => ($this->district != null) ? new DistrictResource($this->district) : new \stdClass(),
        ];
    }
}