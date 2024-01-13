<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = \App\Models\User::where('username', 'andikaa')->first();

        for ($i = 1; $i <= 5; $i++) {
            $contact = new Contact();
            $contact->user_id = $user1->id;
            $contact->first_name = "$user1->username test first name $i";
            $contact->last_name = "$user1->username test last name $i";
            $contact->email = "test$user1->username$i@gmail.com";
            $contact->phone = "11111$i";
            $contact->save();

            for ($j = 1; $j <= 2; $j++) {
                $address = new Address();
                $address->contact_id = $contact->id;
                $address->street = "JL Mawar $j";
                $address->city = "Karawang";
                $address->province = "Jawa Barat";
                $address->country = "Indonesia";
                $address->postal_code = "121212";
                $contact->addresses()->save($address);
            }
        }
    }
}
