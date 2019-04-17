<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use JWTAuth;

class UserController extends Controller
{
    public function signup(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'roleid' => 'required'
        ]);

        $roleid = $request->input('roleid');
//        if ($roleid == 1) {
//            $roleid = 2;
//        }

        $user = new User([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'roleid' => $roleid,
            'phonenumber' => $request->input('phonenumber'),
            'gender' => $request->input('gender'),
            'password' => bcrypt($request->input('password'))
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user'
        ], 200);
    }

    public function signin(Request $request)
    {
//        $this->validate($request, [
//            'email' => 'required|email|unique:users',
//            'password' => 'required'
//        ]);
//
        $credentials = $request->only('email','password');
        try{
            if (!$token = JWTAuth::attempt($credentials)){
                return response()->json([
                    'error' => 'Invalid Credentials'
                ],400);
            }
        } catch (JWTException $exception){
            return response()->json([
                'error' => 'Could not create token'
            ],500);
        }
//        $user = User::where('email', $request->input('email'))->first();
        return response()->json([
            "token" => $token
        ],200);
    }

    public function getInfo(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        return response()->json([
            "user" => $user
        ],200);
    }

    public function updateInfo(Request $request)
    {
        $user = JWTAuth::parseToken()->toUser();
        $user_info = User::where('id',$user->id)->find(1);
        if ($user_info == null){
            return response([
                "message" => "not found"
            ],201);
        }
        $user_info->name = $request->name;
        $user_info->email = $request->email;
        if ($request->password != "" || $request->password != null){
            $user_info->password = $request->password;
        }
        $user_info->phonenumber = $request->phonenumber;
        $user_info->gender = $request->gender;
        $user_info->save();
        return response([
            "message" => "success"
        ],200);
    }
}
