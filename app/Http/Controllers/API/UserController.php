<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function search(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
        ]);
        
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response($response,400);
        }
        $user = User::where('username', 'like', '%' . $request->username . '%')->get();
        return ($user);
    }
}
