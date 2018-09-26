<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\TestEmail;
use Validator;

class ContactController extends BaseController
{

    public function send(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'email'         => 'required|email',
            'first_name'    => 'required',
            'last_name'     => 'required',
            'phone'         => 'required',
            'question'      => 'required'
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $params = $request->all();
        if($params){
            $data = [
                'subject'   => 'Contact Us',
                'from'      => $params['email'],
                'name_from' => $params['first_name'],
                'email'     => $params['email'],
                'password'  => '',
                'last_name' => $params['last_name'],
                'phone'     => $params['phone'],
                'question'  => $params['question'],
                'view'      => 'emails.contact',
            ];
            $email_boxin = 'meidinaisnur@gmail.com';
            Mail::to($email_boxin, 'Boxin Customer Service')->send(new TestEmail($data));
            return response()->json($response = ['message' => 'Success send.'], 200);
        } else {
            return response()->json($response = ['message' => 'Failed.'], 401);
        }
    }

}
