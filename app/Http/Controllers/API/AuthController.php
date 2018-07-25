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

class AuthController extends BaseController
{
    use Notifiable;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'phone' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);

        return (new AuthResource($user))->additional([
            'success' => true,
            'message' => 'User register successfully.',
            'token' => $user->createToken('Boxin')->accessToken,
        ]);

    }

    public function login(Request $request)
    {

        $credentials = $request->validate([
            'phone'    => 'required',
            'password' => 'required',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json([
                'errors' => 'Your credential not macth',
            ], 401);
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
            'phone' => 'required|numeric'
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input              = $request->all();
        $input['password']  = '';
        $input['phone']     = $request->input('phone');
        $user               = User::create($input);
        $token              = $user->createToken('Boxin')->accessToken;

        $data['remember_token'] = $token;
        $remember_token     = User::whereId($user->id)->update($data);

        if($user){
            return (new AuthResource($user))->additional([
                'success' => true,
                'message' => 'User register successfully.',
                'token' => $token,
            ]);
        } else {
            User::whereId($user->id)->delete($user->id);
            return response()->json(['message' => 'Register failed.']);
        }
    }

    public function authCode($code, Request $request)
    {

        $validator = \Validator::make($request->all(), [
            'code_verification' => 'required',
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user_id                  = $request->input('user_id');
        
        if($code == $request->input('code_verification')){
            $data['remember_token']   = NULL;
            $data['status']   = 1;
            $verification     = User::where('id', $user_id)->update($data);
            return $this->sendResponse($verification, 'Authentification success.');
        }else{
            return $this->sendError('Authentification failed, your number wrong. Please try again.');
        }
    }

    public function retryCode($user_id)
    {
        $data               = User::where('id', $user_id)->get();
        $phone              = $data[0]->phone;
        $code               = rand(1000,9999);
        if($data){
            Nexmo::message()->send([
                'to'   => $phone,
                'from' => 'Boxin',
                'text' => 'Please use this number '.$code.' for authentification in Boxin App. Thank you.'
            ]);
            $result = array(
                'user_id' => $user_id,
                'code'    => $code
            );
            return $this->sendResponse($result, 'Success send new code.');
        }else{
            return $this->sendError('Send new code failed.');
        }
    }

}
