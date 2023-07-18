<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:users',
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response($response,400);
        }
        $photoUrl = '';
        if($request->avatar){
        $avatar = $request->avatar;
        $avatar = base64_decode($avatar);
        $newName = time() . "-" . strpos($avatar,0,20) . ".jpg";
        Storage::disk('public')->put('users'.  '/' . $newName , $avatar);
        $photoUrl = url('/storage/users/'.$newName);
        }

        $input = [
            "username" =>$request->username,
            "first_name" =>$request->first_name,
            "last_name" =>$request->last_name,
            "avatar" => $photoUrl,
            "password" =>Hash::make($request->password),
        ];
        if($request->password === $request->confirm_password){
                    $user = User::create($input);
        }
        $success['token'] = $user->createToken(config('app.name'))->plainTextToken;
        $success['user'] = $user;
        
        return response([$success],200);
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);
        if($validator->fails()){
            $response = [
                'success' => false,
                'message' => $validator->errors()
            ];
            return response($response,400);
        }
        if (Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            $user = Auth::user();
            $success['token'] = $user->createToken(config('app.name'))->plainTextToken;
            $success['user'] = $user;
            return response([$success],200);
        } else {
            return response(["message"=>"Не удалось авторизоваться"],400);
        }
    }
    
    public function logout()
    {
        auth()->logout();
        return response(["message"=>"Пользователь вышел успешно"],200);
    }
}
