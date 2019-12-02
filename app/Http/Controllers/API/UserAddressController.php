<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\UserAddressResource;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserAddressController extends BaseController
{
    public function index(Request $request)
    {
        $user = $request->user();
        $userAddress = UserAddress::where('user_id', $user->id)->get();
        if(count($userAddress) > 0) {
            $data = UserAddressResource::collection($userAddress);
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

    public function show($id, Request $request)
    {

        $user = $request->user();
        $userAddress = UserAddress::find($id);
        if($userAddress){
            return (new UserAddressResource($userAddress))->additional([
                'success' => true
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);

    }

    public function store(Request $request)
    {
        $users = $request->user();

        try {

            $request->validate([
                'name'          => 'required',
                'address'       => 'required',
                'postal_code'   => 'required',
                'village_id'    => 'required|exists:villages,id',
            ]);

            $userAddress = UserAddress::create(['user_id'        => $users->id,
                                          'name'            => $request->input('name'),
                                          'address'         => $request->input('address'),
                                          'postal_code'     => $request->input('postal_code'),
                                          'rt'              => $request->input('rt'),
                                          'rw'              => $request->input('rw'),
                                          'village_id'      => $request->input('village_id'),
                                          'apartment_name'  => $request->input('apartment_name'),
                                          'apartment_tower' => $request->input('apartment_tower'),
                                          'apartment_floor' => $request->input('apartment_floor'),
                                          'apartment_number'=> $request->input('apartment_number')]);

            return response()->json([
                'message' => 'Address success creaeted',
                'data' => $userAddress
            ], 200);

        } catch (ValidatorException $e) {
            return response()->json($e);
        }

    }

    public function update($id, Request $request)
    {
        $users = $request->user();

        try {

            $userAddress = UserAddress::find($id);

            $request->validate([
                'name'          => 'required',
                'address'       => 'required',
                'postal_code'   => 'required',
                'village_id'    => 'required|exists:villages,id',
            ]);

            $userAddress->name              = $request->input('name');
            $userAddress->address           = $request->input('address');
            $userAddress->postal_code       = $request->input('postal_code');
            $userAddress->rt                = $request->input('rt');
            $userAddress->rw                = $request->input('rw');
            $userAddress->village_id        = $request->input('village_id');
            $userAddress->apartment_name    = $request->input('apartment_name');
            $userAddress->apartment_tower   = $request->input('apartment_tower');
            $userAddress->apartment_floor   = $request->input('apartment_floor');
            $userAddress->apartment_number  = $request->input('apartment_number');
            $userAddress->save();

            return response()->json([
                'message' => 'Address success updated',
                'data' => $userAddress
            ], 200);

        } catch (ValidatorException $e) {
            return response()->json($e);
        }

    }
}
