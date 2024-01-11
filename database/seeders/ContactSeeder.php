<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user1 = \App\Models\User::where('username', 'andikaa')->first();
        $user2 = \App\Models\User::where('username', 'prakasaaa')->first();

        $andikaContacts = [];
        for ($i = 1; $i <= 10; $i++) {
            $andikaContacts[] = [
                'first_name' => "$user1->username test first $i",
                'last_name' => "$user1->username test last $i",
                'email' => "test$i@gmail.com",
                'phone' => "111111$i",
                'user_id' => $user1->id
            ];
        }

        $prakasaContacts = [];
        for ($i = 1; $i <= 10; $i++) {
            $prakasaContacts[] = [
                'first_name' => "$user2->username test first $i",
                'last_name' => "$user2->username test last $i",
                'email' => "test$i@gmail.com",
                'phone' => "111111$i",
                'user_id' => $user2->id
            ];
        }

        \App\Models\Contact::insert($andikaContacts);
        \App\Models\Contact::insert($prakasaContacts);
    }
}
