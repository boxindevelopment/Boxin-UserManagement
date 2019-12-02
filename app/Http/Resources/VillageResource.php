<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class VillageResource extends Resource
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
            'district' => ($this->district) ? new DistrictResource($this->district) : null
        ];
    }
}
