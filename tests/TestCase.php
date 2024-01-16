<?php

namespace Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    protected function setUp(): void
    {
        parent::setUp();
        DB::delete('delete from addresses');
        DB::delete('delete from contacts');
        DB::delete('delete from users');
        DB::delete('delete from personal_access_tokens');
    }

    protected function userRegister()
    {
        $response = $this->post('/api/users', [
            "username" => "khalilannbiya",
            "name" => "Syeich Khalil Annbiya",
            "password" => "rahasia789*(&@"
        ]);

        return $response["auth"]["token"];
    }

    protected function userRegisterMany()
    {
        $response1 = $this->post('/api/users', [
            "username" => "khalilannbiya",
            "name" => "Syeich Khalil Annbiya",
            "password" => "rahasia789*(&@"
        ]);

        $response2 = $this->post('/api/users', [
            "username" => "andikaa",
            "name" => "Andika",
            "password" => "rahasia789*(&@"
        ]);

        return [
            $response1["auth"]["token"],
            $response2["auth"]["token"]
        ];
    }
}
