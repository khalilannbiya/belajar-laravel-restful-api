<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    public function testCreateAddressSuccess()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();
        $contact = $user->contacts->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "JL Melati Uhuy",
            "city" => "Karawang",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Authorization" => 'testingtoken'
        ])->assertStatus(201)->assertJson([
            "data" => [
                "street" => "JL Melati Uhuy",
                "city" => "Karawang",
                "province" => "Jawa Barat",
                "country" => "Indoenesia",
                "postal_code" => "12121"
            ]
        ]);

        $newAddress = \App\Models\Address::where('contact_id', $contact->id)->first();
        self::assertNotNull($newAddress);
    }

    public function testUser1CannotAddAddressToContactsOwnedByUser2()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $user1 = User::with('contacts')->where('username', 'andikaa')->first();
        $tokenUser1 = $user1->token;

        $user2 = User::with('contacts')->where('username', 'prakasaaa')->first();
        $contactUser2 = $user2->contacts->first();

        $this->post('/api/contacts/' . $contactUser2->id . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Authorization" => $tokenUser1 // Log in as user 1
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testUserCannotAddAddressWithInvalidContactId()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();


        $this->post('/api/contacts/' . $user->id + 1 . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Authorization" => $user->token
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testUserCannotAddAddressWithoutLoggingIn()
    {
        $this->post('/api/contacts/21/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Authorization" => "salahtoken" //invalid token
        ])->assertStatus(401)->assertJson([
            "errors" => [
                "message" => [
                    "unauthorized"
                ]
            ]
        ]);
    }

    public function testFailsToSubmitFormWithRequiredFieldsNotFilled()
    {
        $this->seed(["UserSeeder", "ContactSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();


        $this->post('/api/contacts/' . $user->id . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "", // required
            "postal_code" => "12121"
        ], [
            "Authorization" => $user->token
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "country" => [
                    "The country field is required."
                ]
            ]
        ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed(["UserSeeder", "AddressSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            "Authorization" => $user->token
        ])->assertStatus(200)->assertJson([
            "data" => [
                "street" => $address->street,
                "city" => "Karawang",
                "province" => "Jawa Barat",
                "country" => "Indonesia",
                "postal_code" => "121212"
            ]
        ]);
    }

    public function testGetAddressDataForNonExistingContact()
    {
        $this->seed(["UserSeeder", "AddressSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id + 1 . '/addresses/' . $address->id, [
            "Authorization" => $user->token
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }

    public function testGetAddressDataForNonExistingAddressId()
    {
        $this->seed(["UserSeeder", "AddressSeeder"]);
        $user = User::with('contacts')->where('username', 'andikaa')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id . '/addresses/' . $address->id + 3, [
            "Authorization" => $user->token
        ])->assertStatus(404)->assertJson([
            "errors" => [
                "message" => [
                    "not found"
                ]
            ]
        ]);
    }
}
