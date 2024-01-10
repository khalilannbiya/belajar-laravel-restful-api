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

    public function testUpdateContactSuccess()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
            'Authorization' => 'testingtoken'
        ])->assertStatus(200)->assertJson([
            "data" => [
                "first_name" => "testubah",
                "last_name" => "testubah",
                "email" => "testubah@gmail.com",
                "phone" => "1111112",
            ]
        ]);

        $contactNew = Contact::where('first_name', 'testubah')->first();
        self::assertNotEquals($contact->first_name, $contactNew->first_name);
        self::assertNotEquals($contact->last_name, $contactNew->last_name);
        self::assertNotEquals($contact->email, $contactNew->email);
        self::assertNotEquals($contact->phone, $contactNew->phone);
    }

    public function testUpdateContactFirstNameOnly()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
        ], [
            'Authorization' => 'testingtoken'
        ])->assertStatus(200)->assertJson([
            "data" => [
                "first_name" => "testubah",
                "last_name" => "test",
                "email" => "test@gmail.com",
                "phone" => "111111",
            ]
        ]);

        $contactNew = Contact::where('first_name', 'testubah')->first();
        self::assertNotEquals($contact->first_name, $contactNew->first_name);
        self::assertEquals($contact->last_name, $contactNew->last_name);
        self::assertEquals($contact->email, $contactNew->email);
        self::assertEquals($contact->phone, $contactNew->phone);
    }

    public function testUpdateOtherUserContact()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
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

    public function testUpdateContactNotFound()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id + 1, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
            'Authorization' => 'testingtoken'
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }
}
