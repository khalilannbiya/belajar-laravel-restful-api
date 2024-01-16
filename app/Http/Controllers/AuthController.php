<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AuthController extends Controller
{
    public function login(UserLoginRequest $request)
    {
        $data = $request->all();

        $user = User::where('username', $data["username"])->first();
        if (!$user || !Hash::check($data["password"], $user->password)) {
            throw new HttpResponseException(response([
                "errors" => [
                    "message" => [
                        "username or password wrong."
                    ]
                ]
            ], 401));
        }

        if (Auth::attempt($data)) {
            $authUser = Auth::user();
            $token = $authUser->createToken("MyAuthApp")->plainTextToken;
        }

        return response()->json([
            "success" => true,
            "message" => "User Login Successfully",
            "data" => new UserResource($authUser),
            "auth" => [
                "token" => $token,
                "type" => "Bearer"
            ]
        ]);
    }

    public function register(UserRegisterRequest $request)
    {
        $data = $request->all();

        if (User::where('username', $data["username"])->exists()) {
            throw new HttpResponseException(response([
                "errors" => [
                    "username" => [
                        "Username already registered."
                    ]
                ]
            ], 400));
        }

        $user = new User($data);
        $user->password = Hash::make($data["password"]);
        $user->save();

        $token = $user->createToken("MyAuthApp")->plainTextToken;

        return response()->json([
            "success" => true,
            "message" => "User Created Successfully",
            "data" => new UserResource($user),
            "auth" => [
                "token" => $token,
                "type" => "Bearer"
            ]
        ])->setStatusCode(201);
    }

    public function logout()
    {
        $user = Auth::user();
        $user->tokens()->delete();

        return response()->json([
            "success" => true,
            "message" => "Logout Successfully",
        ]);
    }
}
