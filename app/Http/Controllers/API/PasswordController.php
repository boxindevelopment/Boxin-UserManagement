<?php

namespace App\Http\Controllers\API;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Notifications\Notifiable;
use Nexmo;
use Illuminate\Support\Facades\Mail;
use Hash;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\TestEmail;

class PasswordController extends BaseController
{
    // public function sendEmailReset(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'email' => 'required|email|exists:users,email'
    //     ]);

    //     if($validator->fails()) {
    //         return $this->sendError('Error ', $validator->errors());
    //     }

    //     $response = $this->broker()->sendResetLink(
    //         $request->only('email')
    //     );

    //     return $response == Password::RESET_LINK_SENT
    //         ? response()->json([
    //             'status' => true,
    //             'message' => $response
    //         ])
    //         : response()->json([
    //             'status' => false,
    //             'message' => $response
    //         ]);
    // }

    // public function forgotPassword(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'email' => 'required|email|exists:users,email'
    //     ]);

    //     if($validator->fails()) {
    //         return $this->sendError('Error ', $validator->errors());
    //     }

    //     $params = $request->only('email');
    //     $user = User::where('email','=',$params['email'])->first();
    //     if($user){
    //         $new_password = str_random(6);
    //         $user->password = bcrypt($new_password);
    //         // TODO: create email view for new password
    //         if($user->save()){
    //             // TODO: create email view for new password
    //             Mail::send('emails.password', ['password' => $new_password, 'email' => $user->email], function ($m) use ($user) {
    //                 $m->from('admin@boxin.com', "Boxin Administrator");
    //                 $m->to($user->email, $user->first_name)->subject('Resetting Account Password');
    //             });
    //             return response()->json($response = ['message' => 'Reset password already sent to your email.']);
    //         } else {
    //             return response()->json($response = ['message' => 'Reset password fail.']);
    //         }
    //     }
    //     return response()->json($response = ['message' => 'Your email has not yet registered. Please contact admin!'], 404);
    // }

    public function forgotPassword(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $params = $request->only('email');
        $user = User::where('email','=',$params['email'])->first();
        if($user){
            $new_password = str_random(6);
            $user->password = bcrypt($new_password);
            // TODO: create email view for new password
            if($user->save()){
                // TODO: create email view for new password
                $data = [
                    'email' => $user->email,
                    'subject' => 'Resetting Account Password',
                    'password' => $new_password,
                ];
                Mail::to($user->email, $user->first_name)->send(new TestEmail($data));
                return response()->json($response = ['message' => 'Reset password already sent to your email.']);
            } else {
                return response()->json($response = ['message' => 'Reset password fail.']);
            }
        }
        return response()->json($response = ['message' => 'Your email has not yet registered. Please contact admin!'], 404);
    }

    // public function changePassword(Request $request)
    // {
    //     $validator = \Validator::make($request->all(), [
    //         'token' => 'required',
    //         'email' => 'required|email|exists:users,email',
    //         'password' => 'required|confirmed'
    //     ]);

    //     if($validator->fails()) {
    //         return $this->sendError('Error ', $validator->errors());
    //     }

    //     $response = $this->broker()->reset(
    //         $this->credentials($request), function($user, $password) {
    //             $this->resetPassword($user, $password);
    //         }
    //     );

    //     return $response == Password::PASSWORD_RESET
    //         ? response()->json([
    //             'status' => true,
    //             'message' => $response
    //         ])
    //         : response()->json([
    //             'status' => false,
    //             'message' => $response
    //         ]);
    // }

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

    public function changePassword(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
        ]);

        if($validator->fails()) {
            return $this->sendError('Validation Error ', $validator->errors());
        }

        $params = $request->user();
        //check old pass with current pass
        $check  = Hash::check($request->input('old_password'), $params->password, []);
        //check old pass with new pass
        $check2  = Hash::check($request->input('password'), $params->password, []);
        
        $user   = User::where('id', $params['id'])->first();

        if($check){
            if(!$check2){
                $user->password = bcrypt($request->input('password'));
                if($user->save()){
                    return response()->json($response = ['message' => 'Change password success.'], 200);
                } else {
                    return response()->json($response = ['message' => 'Change password fail.'], 404);
                }
            }else{
                return response()->json($response = ['message' => 'Cannot save, because new password same with current password.'], 404);
            }            
        }else{
            return response()->json($response = ['message' => 'Your old password wrong.'], 404);
        }
    }

}
