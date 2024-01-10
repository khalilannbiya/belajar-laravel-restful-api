<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new \App\Models\User();
        $user->username = "andikaa";
        $user->name = "Andika";
        $user->password = \Illuminate\Support\Facades\Hash::make("inipassword123##");
        $user->token = "testingtoken";
        $user->save();

        $user = new \App\Models\User();
        $user->username = "prakasaaa";
        $user->name = "Prakasa";
        $user->password = \Illuminate\Support\Facades\Hash::make("inipassword123##");
        $user->token = "testingtoken2";
        $user->save();
    }
}
