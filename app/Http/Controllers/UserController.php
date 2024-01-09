<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
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

        $user->token = Str::uuid()->toString();
        $user->save();

        return new UserResource($user);
    }

    public function get()
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request)
    {
        $data = $request->all();

        $user = Auth::user();

        if (isset($data["name"])) {
            $user->name = $data["name"];
        }

        if (isset($data["password"])) {
            $user->password = Hash::make($data["password"]);
        }

        // If there is a warning error for the save() method, please ignore it as it may be due to a VSCode extension issue, specifically Intelephense
        $user->save();

        return new UserResource($user);
    }
}
