<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    public function testGetUserCurrentSuccess(): void
    {
        $token = $this->userRegister();

        $this->get('/api/users/current', [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Get Current Successfully",
            "data" => [
                "name" => "Syeich Khalil Annbiya",
                "username" => "khalilannbiya"
            ]
        ]);
    }

    public function testGetUserCurrentWithoutTokenHeader()
    {
        $this->get('/api/users/current', [
            "Accept" => "application/json"
        ])->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

    public function testGetUserCurrentTokenInvalid()
    {

        $this->get('/api/users/current', [
            "Accept" => "application/json",
            "Authorization" => "invalidtoken123",
        ])->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

    public function testUpdateNameSuccess()
    {
        $token = $this->userRegister();
        $oldUser = User::where('username', 'khalilannbiya')->first();

        $this->put('api/users/current', [
            "name" => "Syeich",
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Update Successfully",
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich",
            ]
        ]);

        $newUser = User::where('username', 'khalilannbiya')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdatePasswordSuccess()
    {
        $token = $this->userRegister();
        $oldUser = User::where('username', 'khalilannbiya')->first();

        $this->put('/api/users/current', [
            "password" => "inipasswordbaru123##",
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Update Successfully",
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya",
            ]
        ]);

        $newUser = User::where('username', 'khalilannbiya')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateFailed()
    {
        $token = $this->userRegister();

        $this->put('/api/users/current', [
            "name" => "Syeich",
            "password" => "inipasswordinvalid"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "password" => [
                    "The password field must contain at least one symbol.",
                    "The password field must contain at least one number."
                ]
            ]
        ]);
    }
}
