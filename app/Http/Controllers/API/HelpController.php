<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\HelpResource;
use App\Models\Help;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HelpController extends BaseController
{
    public function index(Request $request)
    {
        $help = Help::all();
        if(count($help) > 0) {
            $data = HelpResource::collection($help);
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);
    }

    public function show($id, Request $request)
    {

        $help = Help::find($id);
        if($help){
            return (new HelpResource($help))->additional([
                'success' => true
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Data not found'
        ]);

    }

    public function store(Request $request)
    {

        try {

            $request->validate([
                'name'      => 'required',
                'email'     => 'required',
                'subject'   => 'required',
                'message'   => 'required',
            ]);

            $help = Help::create(['name'        => $request->input('name'),
                                  'email'       => $request->input('email'),
                                  'subject'     => $request->input('subject'),
                                  'message'     => $request->input('message')]);

            return response()->json([
                'message' => 'Help success creaeted',
                'data' => $help
            ], 200);

        } catch (ValidatorException $e) {
            return response()->json($e);
        }

    }

    public function update($id, Request $request)
    {

        try {

            $help = Help::find($id);

            $request->validate([
                'name'      => 'required',
                'email'     => 'required',
                'subject'   => 'required',
                'message'   => 'required',
            ]);

            $help->name          = $request->input('name');
            $help->email         = $request->input('email');
            $help->subject       = $request->input('subject');
            $help->message       = $request->input('message');
            $help->save();

            return response()->json([
                'message' => 'Help success updated',
                'data' => $help
            ], 200);

        } catch (ValidatorException $e) {
            return response()->json($e);
        }

    }
}
