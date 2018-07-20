<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordController extends BaseController
{
    public function sendEmailReset(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? response()->json([
                'status' => true,
                'message' => $response
            ])
            : response()->json([
                'status' => false,
                'message' => $response
            ]);
    }

    public function changePassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'token' => 'required',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|confirmed'
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $response = $this->broker()->reset(
            $this->credentials($request), function($user, $password) {
                $this->resetPassword($user, $password);
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json([
                'status' => true,
                'message' => $response
            ])
            : response()->json([
                'status' => false,
                'message' => $response
            ]);
    }

    protected function credentials(Request $request)
    {
        return $request->only(
            'email', 'password', 'password_confirmation', 'token'
        );
    }

    protected function resetPassword($user, $password)
    {
        $user->password = bcrypt($password);
        $user->setRememberToken(Str::random(60));
        $user->save();

        Auth::guard()->login($user);
    }

    protected function broker()
    {
        return Password::broker();
    }
}