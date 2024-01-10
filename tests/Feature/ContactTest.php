<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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

    public function testGetContactSuccess()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'testingtoken'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@gmail.com',
                    'phone' => '111111',
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'testingtoken'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'testingtoken2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'not found'
                    ]
                ]
            ]);
    }
}
