<?php


namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    
    public function toArray($request)
    {
        return [
            'original_name' => $this->original_name,
            'show_name' => $this->show_name,
            'code' => $this->code,
        ];
    }
}
