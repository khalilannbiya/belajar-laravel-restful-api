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
        $token =  $this->userRegister();

        $this->post('/api/contacts', [
            "first_name" => "Indra",
            "last_name" => "Frimawan",
            "email" => "indrafri@gmail.com",
            "phone" => "089329982982"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
        ])->assertStatus(201)->assertJson([
            "success" => true,
            "message" => "Add Contact Successfully",
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
        $token =  $this->userRegister();

        $this->post('/api/contacts', [
            "first_name" => "",
            "last_name" => "Frimawan",
            "email" => "indrafri",
            "phone" => "089329982982"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $token",
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
        $this->post('/api/contacts', [
            "first_name" => "Indra",
            "last_name" => "Frimawan",
            "email" => "indrafri@gmail.com",
            "phone" => "089329982982"
        ], [
            "Accept" => "application/json",
            "Authorization" => "tokensalah",
        ])->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

    public function testGetContactSuccess()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]",
        ])->assertStatus(200)
            ->assertJson([
                "success" => true,
                "message" => "Get Contact Successfully",
                "data" => [
                    "first_name" => "khalilannbiya test first 1",
                    "last_name" => "khalilannbiya test last 1",
                    "email" => "test1@gmail.com",
                    "phone" => "1111111"
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id + 10, [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]",
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[1]",
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Update Contact Successfully",
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Update Contact Successfully",
            "data" => [
                "first_name" => "testubah",
                "last_name" => "khalilannbiya test last 1",
                "email" => "test1@gmail.com",
                "phone" => "1111111"
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[1]"
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id + 10, [
            'first_name' => 'testubah',
            'last_name' => 'testubah',
            'email' => 'testubah@gmail.com',
            'phone' => '1111112',
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testDeleteContactSuccess()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Delete Contact Successfully",
        ]);

        $contactDeleted = Contact::where('id', $contact->id)->first();
        self::assertNull($contactDeleted);
    }

    public function testDeleteContactNotFound()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id + 10, [], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testSearchByEmail()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");

        $this->get('/api/contacts?email=test', [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "data" => [],
            "links" => [],
            "meta" => []
        ]);
    }
}
