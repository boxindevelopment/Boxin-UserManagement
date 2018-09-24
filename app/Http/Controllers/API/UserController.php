<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\AuthResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

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
        $user->first_name   = $request->first_name;
        $user->last_name    = $request->last_name;
        $user->email        = $request->email;
        if ($request->hasFile('image')) {
            if ($request->file('image')->isValid()) {
                $getimageName = time().'.'.$request->image->getClientOriginalExtension();
                $image = $request->image->move(public_path('images/user'), $getimageName);
    
            }
            $user->image = $getimageName;
        }
        
        $user->save();

        return (new AuthResource($user))->additional([
            'success' => true,
            'message' => 'User updated'
        ]);
    }
}
