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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses', [
            "street" => "JL Melati Uhuy",
            "city" => "Karawang",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]",
        ])->assertStatus(201)->assertJson([
            "success" => true,
            "message" => "Add Address Successfully",
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");

        $user2 = User::with('contacts')->where('username', 'andikaa')->first();
        $contactUser2 = $user2->contacts->first();

        $this->post('/api/contacts/' . $contactUser2->id . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]" // Log in as user 1
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
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $user = User::with('contacts')->where('username', 'andikaa')->first();


        $this->post('/api/contacts/' . $user->id + 1 . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
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

    public function testUserCannotAddAddressWithoutLoggingIn()
    {
        $this->post('/api/contacts/21/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "Indoenesia",
            "postal_code" => "12121"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer salahtoken" //invalid token
        ])->assertStatus(401)->assertJson([
            "message" => "Unauthenticated."
        ]);
    }

    public function testFailsToSubmitFormWithRequiredFieldsNotFilled()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("ContactSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();


        $this->post('/api/contacts/' . $user->id . '/addresses', [
            "street" => "JL Mawar",
            "city" => "Jakarta",
            "province" => "Jawa Barat",
            "country" => "", // required
            "postal_code" => "12121"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
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
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Get Address Successfully",
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
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id + 1 . '/addresses/' . $address->id, [
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

    public function testGetAddressDataForNonExistingAddressId()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        $this->get('api/contacts/' . $contact->id . '/addresses/' . $address->id + 3, [
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

    public function testUpdateAddressSuccess()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        self::assertEquals($address->street, "JL Mawar 1");
        self::assertEquals($address->city, "Karawang");
        self::assertEquals($address->province, "Jawa Barat");
        self::assertEquals($address->country, "Indonesia");
        self::assertEquals($address->postal_code, "121212");

        $this->put('api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            "street" => "Jl Rambutan",
            "city" => "Jakarta",
            "province" => "DKI Jakarta",
            "country" => "Indonesia",
            "postal_code" => "90900"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Update Address Successfully",
            "data" => [
                "street" => "Jl Rambutan",
                "city" => "Jakarta",
                "province" => "DKI Jakarta",
                "country" => "Indonesia",
                "postal_code" => "90900"
            ]
        ]);

        $updatedAddress = $contact->addresses()->first();
        self::assertNotEquals($address->street, $updatedAddress->street);
        self::assertNotEquals($address->city, $updatedAddress->city);
        self::assertNotEquals($address->province, $updatedAddress->province);
        self::assertNotEquals($address->postal_code, $updatedAddress->postal_code);
    }

    public function testUpdateAddressFailsWithoutRequiredFields()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        self::assertEquals($address->street, "JL Mawar 1");
        self::assertEquals($address->city, "Karawang");
        self::assertEquals($address->province, "Jawa Barat");
        self::assertEquals($address->country, "Indonesia");
        self::assertEquals($address->postal_code, "121212");

        $this->put('api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            "street" => "Jl Rambutan",
            "city" => "Jakarta",
            "province" => "DKI Jakarta",
            "postal_code" => "90900"
        ], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(400)->assertJson([
            "errors" => [
                "country" => [
                    "The country field is required."
                ]
            ]
        ]);

        $updatedAddress = $contact->addresses()->first();
        self::assertEquals($address->street, $updatedAddress->street);
        self::assertEquals($address->city, $updatedAddress->city);
        self::assertEquals($address->province, $updatedAddress->province);
        self::assertEquals($address->postal_code, $updatedAddress->postal_code);
    }

    public function testDeleteAddressSuccess()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();
        $address = $contact->addresses()->first();

        self::assertNotNull($address);

        $this->delete('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [], [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "success" => true,
            "message" => "Delete Address Successfully",
        ]);

        $deletedAddress = \App\Models\Address::find($address->id);
        self::assertNull($deletedAddress);
    }

    public function testGetListAddresses()
    {
        $tokens = $this->userRegisterMany();
        $this->seed("AddressSeeder");
        $user = User::with('contacts')->where('username', 'khalilannbiya')->first();
        $contact = $user->contacts->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses', [
            "Accept" => "application/json",
            "Authorization" => "Bearer $tokens[0]"
        ])->assertStatus(200)->assertJson([
            "data" => [
                [
                    "street" => "JL Mawar 1",
                    "city" => "Karawang",
                    "province" => "Jawa Barat",
                    "country" => "Indonesia",
                    "postal_code" => "121212"
                ],
                [
                    "street" => "JL Mawar 2",
                    "city" => "Karawang",
                    "province" => "Jawa Barat",
                    "country" => "Indonesia",
                    "postal_code" => "121212"
                ]
            ]
        ]);
    }
}
