<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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

    public function testUserLoginSuccess(): void
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
}
