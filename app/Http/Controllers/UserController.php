<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Exceptions\RouteNotFoundException;
use Faker\Factory as Faker;

class UserController extends Controller
{
    public function dataUser(){
        $users = User::all();
        return new UserResource(true, 'List All User', $users);
    }

    public function createUser(){
        $faker = Faker::create();

        $username = $faker->userName;
        $password = $faker->password;
        $email = $faker->unique()->safeEmail;

        $users = User::create([
            'username' => $username,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'user',
            'created_at' => now()
        ]);
        return new UserResource(true, 'User Berhasil Ditambahkan',
            ['username' => $username,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function deleteUser(string $id){

    }
}
