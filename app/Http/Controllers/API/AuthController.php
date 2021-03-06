<?php
namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use App\Models\UserAddress;
use App\Http\Resources\AuthResource;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Notifications\Notifiable;
use Nexmo;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;
use Twilio\Exceptions\RestException;

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
            return response()->json(['success' => false, 'status_verified' => null, 'message' => 'Your credential not match'], 401);
        }

        $success['token'] =  auth()->user()->createToken('Boxin')->accessToken;
        $success['first_name'] =  auth()->user()->first_name;
        $success['email'] =  auth()->user()->email;
        $success['phone'] =  auth()->user()->phone;

        if (auth()->user()->status != 1) {
          // return response()->json([
          //   'success'         => false,
          //   'status_verified' => 0,
          //   'message'         => 'Account not verified. Please retry code OTP.',
          //   'data'            => new AuthResource(auth()->user())
          // ], 401);
          return (new AuthResource(auth()->user()))->additional([
            'success'         => false,
            'status_verified' => 0,
            'message'         => 'Account not verified. Please retry code OTP.',
            'token_otp'       => auth()->user()->remember_token
          ], 401);
        }
        return (new AuthResource(auth()->user()))->additional([
            'success' => true,
            'message' => 'User login successfully.',
            'token' => auth()->user()->createToken('Boxin')->accessToken,
            'token_otp'       => auth()->user()->remember_token
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
            'email' => 'required|email|unique:users,email,NULL,id,deleted_at,NULL',
            'phone' => 'required|numeric|unique:users,phone,NULL,id,deleted_at,NULL',
            'password' => 'required',
            'confirmation_password' => 'required|same:password',
            'address' => 'required',
            'postal_code'   => 'required',
            'village_id'    => 'required|exists:villages,id',
        ], [
            'email.unique' => 'email already registered',
            'phone.unique' => 'phone number already registered',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $check = User::where('email', $request->input('email'))
                     ->whereOr('phone', $request->input('phone'))
                     ->first();
        if($check){
            $check->deleted_at  = null;
            $check->password    = bcrypt($request->input('password'));
            $check->email       = $request->input('email');
            $check->last_name   = $request->input('last_name');
            $check->phone       = $request->input('phone');
            $check->status      = 2;

            $check->save();
            $user               = User::find($check->id);
        } else {
            $input              = $request->all();
            $input['password']  = bcrypt($request->input('password'));
            $input['email'] = $request->input('email');
            $input['last_name'] = $request->input('last_name');
            $input['phone']     = $request->input('phone');
            $input['status']    = 2;
            $user               = User::create($input);
        }

        $token              = $user->createToken('Boxin')->accessToken;

        $data['remember_token'] = $token;
        $remember_token     = User::whereId($user->id)->update($data);

        if($user){


          // $code = rand(1000,9999);
          // $user->remember_token = $code;
          // $user->save();
          //   try {
          //       Nexmo::message()->send([
          //           'to'   => $input['phone'],
          //           'from' => 'Boxin',
          //           'text' => 'Please use this number '.$code.' for authentication in Boxin App. Thank you.'
          //       ]);
          //   } catch (Nexmo\Client\Exception\Request $e) {
          //   }

          $userAddress = UserAddress::create(['user_id'   => $user->id,
                                        'name'            => $request->input('first_name'),
                                        'address'         => $request->input('address'),
                                        'postal_code'     => $request->input('postal_code'),
                                        'rt'              => $request->input('rt'),
                                        'rw'              => $request->input('rw'),
                                        'village_id'      => $request->input('village_id'),
                                        'apartment_name'  => $request->input('apartment_name'),
                                        'apartment_tower' => $request->input('apartment_tower'),
                                        'apartment_floor' => $request->input('apartment_floor'),
                                        'apartment_number'=> $request->input('apartment_number'),
                                        'default'         => true]);

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

    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required',
            'token'     => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        $client     = new Client($accountSid, $authToken, $appSid);

        $user = User::where('id', $request->user_id)->where('remember_token', $request->token)->first();
        if ($user) {
            try {

                $code = rand(1000,9999);
                $user->code = $code;
                $user->save();

                $token = $user->createToken('Boxin')->accessToken;

                $phone = '+'.$user->phone;
                // Use the client to do fun stuff like send text messages!
                $client->messages->create(
                    $phone, array(
                        'from' => config('app.twilio')['TWILIO_NUMBER'],
                        'body' => 'Please use this number '.$code.' for authentication in Boxin App. Thank you.'
                    )
                );

                return (new AuthResource($user))->additional([
                    'success' => true,
                    'message' => 'Send code successfully.',
                    'token' => $token,
                ]);
            } catch (RestException $e){
                // echo "Error: " . $e->getMessage();
                // User::where('id', $request->user_id)->delete();
                return response()->json(['success' => false, 'message' =>'Phone number '.$phone.' is not a correct mobile phone number. Please try again!', 'e' => $e->getMessage()]);
            } catch (Exception $e){
                // echo "Error: " . $e->getMessage();
                // User::where('id', $request->user_id)->delete();
                return response()->json(['success' => false, 'message' =>'Phone number '.$phone.' is not a correct mobile phone number. Please try again!']);
            }
        } else {
            return response()->json(['success' => false, 'message' => 'Send code failed.']);
            User::whereId($user->id)->delete($user->id);
        }

        // else {
        //   User::where('id', $request->user_id)->delete();
        //   return response()->json(['success' => false, 'message' => 'Send code failed.']);
        //     // User::whereId($user->id)->delete($user->id);
        // }
        return response()->json(['success' => false, 'message' => 'Send code failed.']);
    }

    public function authCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'code_verification' => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $user       = User::where('id', $request->input('user_id'))->first();
        if($user){
            if($user->code == $request->input('code_verification')){
                $user->status           = 1;
                $user->save();
                return (new AuthResource($user))->additional([
                    'success' => true,
                    'message' => 'Authentication success.'
                ]);
            }else{
                return $this->sendError('Authentication failed, your number wrong. Please try again.');
            }
        }
    }

    public function retryCode(Request $request)
    {
        $accountSid = config('app.twilio')['TWILIO_ACCOUNT_SID'];
        $authToken  = config('app.twilio')['TWILIO_AUTH_TOKEN'];
        $appSid     = config('app.twilio')['TWILIO_APP_SID'];
        $number_twilio = config('app.twilio')['TWILIO_NUMBER'];
        $client     = new Client($accountSid, $authToken);

        $phone      = $request->input('phone');
        $data       = User::where('phone', $phone)->first();
        $code       = rand(1000,9999);
        if($data){

            $data->code = $code;
            $data->save();

            $nomor = '+'.$phone;
            $client->messages->create(
                $nomor, array(
                    // 'from' => '+16105491019',
                    'from' => $number_twilio,
                    'body' => 'Please use this number '.$code.' for authentication in Boxin App. Thank you.'
                )
            );

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

}
