<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Notifications\Notifiable;
use Nexmo;
use Sms;

class AuthController extends BaseController
{
    use Notifiable;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'status_verified' => null, 
                'message' => 'Your credential not match',
            ], 401);
        }

        if (auth()->user()->status != 1) {
            return response()->json([
                'success' => 'false', 
                'status_verified' => 0, 
                'message' => 'Account not verified. Please retry code OTP.'], 402);
        }

        $success['token'] =  auth()->user()->createToken('Boxin')->accessToken;
        $success['first_name'] =  auth()->user()->first_name;
        $success['email'] =  auth()->user()->email;
        $success['phone'] =  auth()->user()->phone;

        // return $this->sendResponse($success, 'User login successfully.');

        return (new AuthResource(auth()->user()))->additional([
            'success' => true,
            'message' => 'User login successfully.',
            'token' => auth()->user()->createToken('Boxin')->accessToken,
        ]);
    }

    public function refreshToken()
    {
        //
    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|unique:users',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input              = $request->all();
        $input['password']   = bcrypt($request->input('password'));
        $input['phone']     = $request->input('phone');
        $user               = User::create($input);
        $token              = $user->createToken('Boxin')->accessToken;

        $data['remember_token'] = $token;
        $remember_token     = User::whereId($user->id)->update($data);

        if($user){


          $code = rand(1000,9999);
          $user->remember_token = $code;
          $user->save();
            try {
                Nexmo::message()->send([
                    'to'   => $input['phone'],
                    'from' => 'Boxin',
                    'text' => 'Please use this number '.$code.' for authentification in Boxin App. Thank you.'
                ]);
            } catch (Nexmo\Client\Exception\Request $e) {
            }

            return (new AuthResource($user))->additional([
                'success' => true,
                'message' => 'User register successfully.',
                'token' => $token,
                'code' => $code,
            ]);
        } else {
            User::whereId($user->id)->delete($user->id);
            return response()->json(['message' => 'Register failed.']);
        }
    }

    public function authCode(Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'code_verification' => 'required',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user       = User::where('id', $request->input('user_id'))->first();

        if($user->remember_token == $request->input('code_verification')){
            $data['remember_token']   = NULL;
            $data['status']   = 1;
            $verification     = User::where('id', $user->id)->update($data);
            // return $this->sendResponse($verification, 'Authentification success.');
            return (new AuthResource($user))->additional([
                'success' => true,
                'message' => 'Authentification success.'
            ]);
        }else{
            return $this->sendError('Authentification failed, your number wrong. Please try again.');
        }
    }

    public function retryCode(Request $request)
    {

        $phone              = $request->input('phone');
        $data               = User::where('phone', $phone)->first();
        $code               = rand(1000,9999);
        if($data){
            $data->remember_token = $code;
            $data->save();
            Nexmo::message()->send([
                'to'   => $data->phone,
                'from' => 'Boxin',
                'text' => 'Please use this number '.$code.' for authentification in Boxin App. Thank you.'
            ]);
            $result = array(
                'user_id' => $data->id,
                'phone'   => $data->phone,
                'code'    => $code
            );
            return $this->sendResponse($result, 'Success send new code.');
        }else{
            return $this->sendError('Send new code failed.');
        }

    }

    // public function retryCode($user_id)
    // {
    //     $data               = User::where('id', $user_id)->get();
    //     $phone              = $data[0]->phone;
    //     $code               = rand(1000,9999);
    //     if($data){
    //         $message  = "Hello Phone!";
    //         $to       = "+6281221819612";
    //         $from     = "+6281221819612";
    //         $response = Sms::send($message,$to,$from);
    //         dd($response);
    //         $result = array(
    //             'user_id' => $user_id,
    //             'code'    => $code,
    //             'response'=> $response,
    //         );
    //         return $this->sendResponse($result, 'Success send new code.');
    //     }else{
    //         return $this->sendError('Send new code failed.');
    //     }
    // }

}
