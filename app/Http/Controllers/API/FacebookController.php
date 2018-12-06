<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;
use Validator;

class FacebookController extends BaseController
{

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $graphUrl = 'https://graph.facebook.com';
    protected $version = 'v2.10';
    protected $fields = ['name', 'email', 'gender', 'verified', 'first_name', 'last_name']; //, 'link'


    public function getToken($token)
    {

        try {
            $meUrl = $this->graphUrl.'/'.$this->version.'/me?access_token='.$token.'&fields='.implode(',', $this->fields);

            if (! empty($this->clientSecret)) {
                $appSecretProof = hash_hmac('sha256', $token, $this->clientSecret);

                $meUrl .= '&appsecret_proof='.$appSecretProof;
            }

            $options = array(
                'http'=>array(
                    'method'=>"GET",
                    'header'=>"Accept: application/json"
                )
            );

            $context  = stream_context_create($options);

            if (! $response = @file_get_contents($meUrl, false, $context)) {
                return response()->json(['error' => 'token fail'], 401);
            }

        } catch (Exception $ex) {
            return response()->json(['error' => 'could_not_send_token'], 500);
        }

        $response = json_decode($response, true);

        if(isset($response['id'])) {

            // Find user by email
            $userByEmail = User::where('email', $response['email'])->first();
            $userByIdFacebook =  User::where('facebook_id', $response['id'])->first();

            if (!is_null($userByEmail)) {
                $userByEmail->facebook_id = $response['id'];
                $userByEmail->save();
                return $this->resultToken($userByEmail);
            } else if(!is_null($userByIdFacebook)) {
                return $this->resultToken($userByIdFacebook);
            } else {

              // dd($response);

                // $names = explode(" ", $response['name']);
                // $lastName = array_pop($names);
                // $firstName = implode(" ", $names);
                //
                // $password = bcrypt($firstName);
                //
                // $input = array(
                //                 'first_name' => $firstName,
                //                 'last_name' => $lastName,
                //                 'email' => $response['email'],
                //                 'password' => $password,
                //                 'facebook_id' => $response['id'],
                //                 'status' => 1,
                //             );
                //tem
                //   $user = User::create($input);
                //   return $this->resultToken($user);
                    return $response;
            }


        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'facebook id not found'
            ], 400);
        }

    }

    public function signUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'facebook_id' => 'required',
            'first_name' => 'required',
            'email' => 'required|email|unique:users',
            'phone' => 'required|numeric|unique:users',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input                  = $request->all();
        $input['password']      = bcrypt($request->input('first_name'));
        $input['last_name']     = $request->input('last_name');
        $input['phone']         = $request->input('phone');
        $input['status']        = 1;
        $user                   = User::create($input);
        $user->facebook_id      = $request->input('facebook_id');
        $user->save();
        $token                  = $user->createToken('Boxin')->accessToken;

        $data['remember_token'] = $token;
        $remember_token         = User::whereId($user->id)->update($data);

        if($user){

            return (new AuthResource($user))->additional([
                'success'       => true,
                'message'       => 'User register successfully.',
                'token'         => $token,
            ]);
        } else {
            User::whereId($user->id)->delete($user->id);
            return response()->json(['message' => 'Register failed.']);
        }
    }

    private function resultToken($user)
    {
        return (new AuthResource($user))->additional([
            'success'           => true,
            'message'           => 'User register successfully.',
            'token'             => $user->createToken('Boxin')->accessToken,
        ]);

    }
}
