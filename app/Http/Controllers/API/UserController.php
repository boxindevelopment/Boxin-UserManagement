<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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
            'name' => 'required',
            'phone' => 'required|unique:users,phone,'. $request->user()->id,
            'email' => 'required|email|unique:users,email,'. $request->user()->id
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $user = User::findOrFail($user->id);
        $user->name = $request->name;
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

    public function sendEmailReset(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $user = User::where('email', $request->email)->first();

        if($user) {
            $response = $this->broker()->sendResetLink(
                $request->only('email')
            );

            return $response == Password::RESET_LINK_SENT
                ? response()->json(['status' => $response])
                : response()->json(['email' => $response]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }

    protected function broker()
    {
        return Password::broker();
    }
}