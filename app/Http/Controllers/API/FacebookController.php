<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;

class FacebookController extends BaseController
{

    /**
     * The user fields being requested.
     *
     * @var array
     */
    protected $graphUrl = 'https://graph.facebook.com';
    protected $version = 'v2.10';
    protected $fields = ['name', 'email', 'gender', 'verified', 'link'];


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

                $gender = ($response['gender'] == 'male') ? 1 : 0;
                $names = explode(" ", $response['name']);
                $lastName = array_pop($names);
                $firstName = implode(" ", $names);

                $password = bcrypt($firstName);

                $input = array(
                                'first_name' => $firstName,
                                'last_name' => $lastName,
                                'email' => $response['email'],
                                'password' => $password,
                                'facebook_id' => $response['id']
                            );

                  $user = User::create($input);
                  return $this->resultToken($user);
            }


        }

    }

    private function resultToken($user)
    {
        return (new AuthResource($user))->additional([
            'success' => true,
            'message' => 'User register successfully.',
            'token' => $user->createToken('Boxin')->accessToken,
        ]);

    }
}
