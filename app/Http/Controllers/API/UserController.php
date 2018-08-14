<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseController
{
    public function show(Request $request)
    {
        $user = $request->user();

        return (new AuthResource($user))->additional([
            'success' => true
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required',
            'phone' => 'required|unique:users,phone,'. $request->user()->id,
            'email' => 'required|email|unique:users,email,'. $request->user()->id
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $user = User::findOrFail($user->id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if($request->password != null) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        return (new AuthResource($user))->additional([
            'success' => true,
            'message' => 'User updated'
        ]);
    }
}
