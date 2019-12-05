<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class UserAddressResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'postal_code' => $this->postal_code,
            'apartment_name' => $this->apartment_name,
            'apartment_tower' => $this->apartment_tower,
            'apartment_floor' => $this->apartment_floor,
            'apartment_number' => $this->apartment_number,
            'rt' => $this->rt,
            'rw' => $this->rw,
            'village' => ($this->village) ? new VillageResource($this->village) : null
        ];
    }
}
