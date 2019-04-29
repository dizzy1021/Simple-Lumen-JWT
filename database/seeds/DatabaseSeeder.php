<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Webpatser\Uuid\Uuid;
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');

        $users = [[
            'id' => Uuid::generate(5,'superadmin', Uuid::NS_DNS)->string,
            'username' => "superadmin",
            'email' => "superadmin@test.com",
            'firstname'=> "John",
            'lastname'=>"Doe",
            'password' => Hash::make("superadmin"),
            'role' => "Super Admin"
        ],
        [
            'id' => Uuid::generate(5,'admin', Uuid::NS_DNS)->string,
            'username' => "admin",
            'firstname'=> "Hamba",
            'lastname'=>"Allah",
            'email' => "admin@test.com",
            'password' => Hash::make("admin"),
            'role' => "Admin"
        ],
        [
            'id' => Uuid::generate(5,'user', Uuid::NS_DNS)->string,
            'username' => "user",
            'firstname'=> "Fulan",
            'lastname'=>"Fulan",
            'email' => "user@test.com",
            'password' => Hash::make("user"),
            'role' => "User"
        ]];
        DB::table('users')->insert($users);
    }
}
