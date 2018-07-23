<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Nexmo;
class AuthController extends BaseController
{
    use Notifiable;
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    // public function register(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'name' => 'required',
    //         'phone' => 'required|unique:users',
    //         'email' => 'required|email|unique:users',
    //         'password' => 'required',
    //         'confirmation_password' => 'required|same:password',
    //     ]);


    //     if($validator->fails()){
    //         return $this->sendError('Validation Error.', $validator->errors());
    //     }


    //     $input = $request->all();
    //     $input['password'] = bcrypt($input['password']);
    //     $user = User::create($input);

    //     return (new AuthResource($user))->additional([
    //         'success' => true,
    //         'message' => 'User register successfully.',
    //         'token' => $user->createToken('Boxin')->accessToken,
    //     ]);

    // }

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
        ]);


        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }


        $input              = $request->all();
        $input['password']  = '';
        $input['phone']     = $request->input('phone');
        $user               = User::create($input);
        $token              = JWTAuth::fromUser($user);

        $data['remember_token'] = $token;
        $remember_token     = User::whereId($user->id)->update($data);

        

        if($user){
            Nexmo::message()->send([
                'to'   => $input['phone'],
                'from' => 'Boxin',
                'text' => 'Please input this verification code.'
            ]);
            return (new AuthResource($user))->additional([
                'success' => true,
                'message' => 'User register successfully.',
                'token' => $token,
            ]);
        } else {
            return response()->json(['message' => 'Register failed.']);
        }
        

    }

    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->content('Your SMS message content');
    }
}
