<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AuthResource;

class GoogleController extends BaseController
{

    public function getToken($token)
    {

        try {

            $meUrl = 'https://www.googleapis.com/oauth2/v3/tokeninfo?id_token='.$token;

            $options = array(
                'http'=>array(
                    'method'=>"GET"
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

        if(isset($response['email'])) {

            // Find user by email
            $user = User::where('email', $response['email'])->first();

            if (is_null($user)) {

                // $gender = (isset($response['gender'])) ? (($response['gender'] == 'male') ? 1 : 0) : '';
                $name = (isset($response['given_name'])) ? $response['given_name'] : '';

                // $image_url = (isset($response['picture'])) ? $response['picture'] : '';

                $password = bcrypt($response['email']);

                $input = array(
                                'name' => $name,
                                'email' => $response['email'],
                                'password' => $password,
                                'google_id' => $response['sub']
                            );

                $user = User::create($input);
                return $this->resultToken($user);

            }

            return $this->login($user);

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
