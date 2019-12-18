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
            'address' => (count($this->addresses) > 0) ? $this->addresses[0]->address : null,
            'postal_code' => (count($this->addresses) > 0) ? $this->addresses[0]->postal_code : null,
            'apartment_name' => (count($this->addresses) > 0) ? $this->addresses[0]->apartment_name : null,
            'apartment_tower' => (count($this->addresses) > 0) ? $this->addresses[0]->apartment_tower : null,
            'apartment_floor' => (count($this->addresses) > 0) ? $this->addresses[0]->apartment_floor : null,
            'apartment_number' => (count($this->addresses) > 0) ? $this->addresses[0]->apartment_number : null,
            'rt' => (count($this->addresses) > 0) ? $this->addresses[0]->rt : null,
            'rw' => (count($this->addresses) > 0) ? $this->addresses[0]->rw : null,
            'village' => (count($this->addresses) > 0) ? new VillageResource($this->addresses[0]->village) : null,
            'token' => $this->remember_token,
        ];
    }
}
