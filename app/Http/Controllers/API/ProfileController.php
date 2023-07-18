<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        if ($user) {
            return $user;
        }
        return response(['message'=>'Пользователь не найден'],200);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendError('Вы можете редактировать только свой профиль');
        }
        if($request->has('password')){
            $user->password = Hash::make($request->password);
        }
        if($request->avatar){
            $avatar = $request->avatar;
            $avatar = base64_decode($avatar);
            $newName = time() . "-" . strpos($avatar,0,20) . ".jpg";
            Storage::disk('public')->put('users'.  '/' . $newName , $avatar);
            $photoUrl = url('/storage/users/'.$newName);
            $user->avatar = $photoUrl;
        }
        $user->username = $request->username;
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->update();
        $user->save();
        return response(["user"=>$user,'message' => 'Данные успешно обновлены!']);
    }
}
