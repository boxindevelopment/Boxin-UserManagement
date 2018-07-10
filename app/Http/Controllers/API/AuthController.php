<?php


namespace App\Http\Controllers\API;


use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Validator;


class AuthController extends BaseController
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
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
        $success['name'] =  auth()->user()->name;
        $success['email'] =  auth()->user()->email;
        $success['phone'] =  auth()->user()->phone;

        // return $this->sendResponse($success, 'User login successfully.');

        return (new AuthResource(auth()->user()))->additional([
            'success' => true,
            'message' => 'User login successfully.',
            'token' => auth()->user()->createToken('Boxin')->accessToken,
        ]);
    }
}
