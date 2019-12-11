<?php


namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $this->name,
            'user_name' => $this->user_name,
            'email' => $this->email,
            'address_detail' => ($this->address_detail != null) ? $this->address_detail != null: '',
            'avatar' => $this->avatar,
            'type' => $this->type,
            'city_code' => ($this->code_address != null) ? $this->code_address : '' ,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'district_code' => ($this->district_code != null) ?  $this->district_code : '',
            'type_account' => $this->type_account,
        ];
    }
}