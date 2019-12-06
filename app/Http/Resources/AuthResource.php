<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class AuthResource extends Resource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status,
            'image' => is_null($this->image) ? null : (asset('images/user').'/'.$this->image),
            'address' => ($this->addresses) ? $this->addresses[0]->address : null,
            'postal_code' => ($this->addresses) ? $this->addresses[0]->postal_code : null,
            'apartment_name' => ($this->addresses) ? $this->addresses[0]->apartment_name : null,
            'apartment_tower' => ($this->addresses) ? $this->addresses[0]->apartment_tower : null,
            'apartment_floor' => ($this->addresses) ? $this->addresses[0]->apartment_floor : null,
            'apartment_number' => ($this->addresses) ? $this->addresses[0]->apartment_number : null,
            'rt' => ($this->addresses) ? $this->addresses[0]->rt : null,
            'rw' => ($this->addresses) ? $this->addresses[0]->rw : null,
            'village' => ($this->addresses) ? new VillageResource($this->addresses[0]->village) : null,
            'token' => $this->remember_token,
        ];
    }
}
