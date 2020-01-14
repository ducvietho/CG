<?php


namespace App\Http\Resources\CMS;


use Illuminate\Http\Resources\Json\JsonResource;

class UserCMSResource extends JsonResource
{
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'user_id' => $this->user_id,
            'email' => $this->email,
            'phone' => $this->phone,
            'type' => $this->type,
            'gender' => $this->gender,
            'type_account' => $this->type_account,
            'birthday' => $this->birthday,
        ];
    }
}