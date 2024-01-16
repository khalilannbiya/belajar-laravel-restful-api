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

    public function get()
    {
        $user = Auth::user();
        return response()->json([
            "success" => true,
            "message" => "Get Current Successfully",
            "data" => new UserResource($user),
        ]);
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

        return response()->json([
            "success" => true,
            "message" => "Update Successfully",
            "data" => new UserResource($user),
        ]);
    }
}
