<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use File;

class UserController extends BaseController
{
    public function show(Request $request)
    {
        $user = $request->user();

        return (new AuthResource($user))->additional([
            'success' => true
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validator = \Validator::make($request->all(), [
            'first_name' => 'required',
            // 'phone' => 'required|unique:users,phone,'. $request->user()->id,
            'email' => 'required|email|unique:users,email,'. $request->user()->id
        ]);

        if($validator->fails()) {
            return $this->sendError('Error ', $validator->errors());
        }

        $user   = User::findOrFail($user->id);
        $image_old          = $user->image;
        $image              = $request->image;
        
        if($user){
            $user->first_name   = $request->first_name;
            $user->last_name    = $request->last_name;
            $user->email        = $request->email;
            if($image){
                if ($request->hasFile('image')) {
                    $image_path = "/images/user/{$image_old}";
                    if ($request->file('image')->isValid()) {
                        if (file_exists(public_path().$image_path)) {
                           unlink(public_path().$image_path);
                        }
                        $getimageName = time().'.'.$request->image->getClientOriginalExtension();
                        $image = $request->image->move(public_path('images/user'), $getimageName);
                    }
                }
                $user->image = $getimageName != '' ? $getimageName : $image_old;
            }
            $user->save();
        }        

        return (new AuthResource($user))->additional([
            'success' => true,
            'message' => 'User updated'
        ]);
    }
}
