<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\VillageResource;
use App\Http\Resources\DistrictResource;
use App\Http\Resources\RegencyResource;
use App\Http\Resources\ProvinceResource;
use App\Models\Village;
use App\Models\District;
use App\Models\Regency;
use App\Models\Province;
use Illuminate\Http\Request;

class VillageController extends BaseController
{
    public function getAllVillages($district_id)
    {
        $villages = Village::where('district_id', $district_id)->get();
        if(count($villages) > 0) {
            $data = VillageResource::collection($villages);
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }

    public function getAllDistricts($regency_id)
    {
        $districts = District::where('regency_id', $regency_id)->get();
        if(count($districts) > 0) {
            $data = DistrictResource::collection($districts);
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }

    public function getAllRegencies($province_id)
    {
        $regencies = Regency::where('province_id', $province_id)->get();
        if(count($regencies) > 0) {
            $data = RegencyResource::collection($regencies);
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }

    public function getAllProvinces()
    {
        $provinces = Province::all();
        if(count($provinces) > 0) {
            $data = ProvinceResource::collection($provinces);
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }
}
