<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testUserRegisterSuccess(): void
    {
        $this->post('/api/users', [
            "username" => "khalilannbiya",
            "name" => "Syeich Khalil Annbiya",
            "password" => "rahasia789*(&@"
        ])->assertStatus(201)->assertJson([
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya",
            ]
        ]);
    }

    public function testUserRegisterFailed(): void
    {
        $this->post('/api/users', [
            "username" => "",
            "name" => "",
            "password" => "rahasia789"
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "The username field is required."
                ],
                "name" => [
                    "The name field is required."
                ],
                "password" => [
                    "The password field must contain at least one symbol."
                ]
            ]
        ]);
    }

    public function testUserRegisterUsernameAlreadyExists(): void
    {
        $this->testUserRegisterSuccess();

        $this->post('/api/users', [
            "username" => "khalilannbiya",
            "name" => "syeich khalil annbiya",
            "password" => "rahasia789*(&@"
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "username" => [
                    "Username already registered."
                ]
            ]
        ]);
    }

    public function testUserLoginSuccess()
    {
        $this->testUserRegisterSuccess();

        $this->post('/api/users/login', [
            "username" => "khalilannbiya",
            "password" => "rahasia789*(&@"
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya",
            ]
        ]);

        $user = \App\Models\User::where('username', 'khalilannbiya')->first();
        self::assertNotNull($user->token);

        return $user->token;
    }

    public function testUserLoginUsernameWrong(): void
    {
        $this->testUserRegisterSuccess();

        $this->post('/api/users/login', [
            "username" => "uhyyu",
            "password" => "rahasia789*(&@"
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password wrong."
                ]
            ]
        ]);
    }

    public function testUserLoginPasswordWrong(): void
    {
        $this->testUserRegisterSuccess();

        $this->post('/api/users/login', [
            "username" => "khalilannbiya",
            "password" => "salahpassword"
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "username or password wrong."
                ]
            ]
        ]);
    }

    public function testGetUserCurrentSuccess(): void
    {
        $token = $this->testUserLoginSuccess();

        $this->get('/api/users/current', [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya",
                "token" => $token
            ]
        ]);
    }

    public function testGetUserCurrentWithoutTokenHeader()
    {
        $this->get('/api/users/current')->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }

    public function testGetUserCurrentTokenInvalid()
    {

        $this->get('/api/users/current', [
            "Authorization" => "invalidtoken123",
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }

    public function testUpdateNameSuccess()
    {
        $token =  $this->testUserLoginSuccess();
        $oldUser = User::where('username', 'khalilannbiya')->first();

        $this->put('/api/users/current', [
            "name" => "Syeich",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
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
        $token =  $this->testUserLoginSuccess();
        $oldUser = User::where('username', 'khalilannbiya')->first();

        $this->put('/api/users/current', [
            "password" => "inipasswordbaru123##",
        ], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
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
        $token =  $this->testUserLoginSuccess();

        $this->put('/api/users/current', [
            "name" => "Syeich",
            "password" => "inipasswordinvalid"
        ], [
            "Authorization" => $token,
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "password" => [
                    "The password field must contain at least one symbol.",
                    "The password field must contain at least one number."
                ]
            ]
        ]);
    }

    public function testLogoutSuccess()
    {
        $token =  $this->testUserLoginSuccess();

        $this->delete('/api/users/logout', [], [
            "Authorization" => $token,
        ])->assertStatus(200)->assertJson([
            "data" => true
        ]);
    }

    public function testLogoutFailed()
    {
        $this->delete('/api/users/logout', [], [
            "Authorization" => "salah",
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }
}
