<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateContactSuccess()
    {
        $this->seed("UserSeeder");

        $this->post('/api/contacts', [
            "first_name" => "Indra",
            "last_name" => "Frimawan",
            "email" => "indrafri@gmail.com",
            "phone" => "089329982982"
        ], [
            "Authorization" => "testingtoken",
        ])->assertStatus(201)->assertJson([
            "data" => [
                "first_name" => "Indra",
                "last_name" => "Frimawan",
                "email" => "indrafri@gmail.com",
                "phone" => "089329982982"
            ]
        ]);
    }

    public function testCreateContactFailed()
    {
        $this->seed("UserSeeder");

        $this->post('/api/contacts', [
            "first_name" => "",
            "last_name" => "Frimawan",
            "email" => "indrafri",
            "phone" => "089329982982"
        ], [
            "Authorization" => "testingtoken",
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "first_name" => [
                    "The first name field is required."
                ],
                "email" => [
                    "The email field must be a valid email address."
                ]
            ]
        ]);
    }

    public function testCreateContactUnauthorized()
    {
        $this->seed("UserSeeder");

        $this->post('/api/contacts', [
            "first_name" => "Indra",
            "last_name" => "Frimawan",
            "email" => "indrafri@gmail.com",
            "phone" => "089329982982"
        ], [
            "Authorization" => "tokensalah",
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }
}
