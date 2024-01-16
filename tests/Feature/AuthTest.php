<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function testUserRegisterSuccess(): void
    {
        $this->post('/api/users', [
            "username" => "khalilannbiya",
            "name" => "Syeich Khalil Annbiya",
            "password" => "rahasia789*(&@"
        ])->assertStatus(201)->assertJson([
            "success" => true,
            "message" => "User Created Successfully",
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya"
            ],
            "auth" => [
                "token" => true,
                "type" => "Bearer"
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

        $response =  $this->post('/api/users/login', [
            "username" => "khalilannbiya",
            "password" => "rahasia789*(&@"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "User Login Successfully",
            "data" => [
                "username" => "khalilannbiya",
                "name" => "Syeich Khalil Annbiya"
            ],
            "auth" => [
                "token" => true,
                "type" => "Bearer"
            ]
        ]);
        return $response["auth"]["token"];
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

    public function testLogoutSuccess()
    {
        $token =  $this->testUserLoginSuccess();

        $this->delete('/api/users/logout', [], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Logout Successfully",
        ]);
    }

    public function testLogoutFailed()
    {
        $this->delete('/api/users/logout', [], [
            "Accept" => "application/json",
            "Authorization" => "Bearer 123",
        ])->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }
}
