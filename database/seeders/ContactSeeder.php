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
        $user1 = \App\Models\User::where('username', 'khalilannbiya')->first();
        $user2 = \App\Models\User::where('username', 'andikaa')->first();

        $khalilannbiyaContacts = [];
        for ($i = 1; $i <= 10; $i++) {
            $khalilannbiyaContacts[] = [
                'first_name' => "$user1->username test first $i",
                'last_name' => "$user1->username test last $i",
                'email' => "test$i@gmail.com",
                'phone' => "111111$i",
                'user_id' => $user1->id
            ];
        }

        $andikaaContacts = [];
        for ($i = 1; $i <= 10; $i++) {
            $andikaaContacts[] = [
                'first_name' => "$user2->username test first $i",
                'last_name' => "$user2->username test last $i",
                'email' => "test$i@gmail.com",
                'phone' => "111111$i",
                'user_id' => $user2->id
            ];
        }

        \App\Models\Contact::insert($khalilannbiyaContacts);
        \App\Models\Contact::insert($andikaaContacts);
    }
}
