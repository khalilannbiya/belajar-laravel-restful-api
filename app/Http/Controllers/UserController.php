<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRegisterRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UserController extends Controller
{
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

        // return new UserResource($user); // response status code also 201
        return (new UserResource($user))->response()->setStatusCode(201);
    }
}
