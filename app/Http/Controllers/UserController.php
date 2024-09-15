<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;
use Faker\Factory as Faker;

class UserController extends Controller
{
    public function dataUser(){
        $users = User::where('role', 'user')->get();
        return new UserResource(true, 'List All User', true, $users);
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
        return new UserResource(true, 'User Berhasil Ditambahkan', false,
            ['username' => $username,
            'email' => $email,
            'password' => $password
        ]);
    }

    public function deleteUser(string $encryptedId){
        $id = Crypt::decryptString($encryptedId);
        $user = User::find($id);

        //delete post
        $user->delete();

        //return response
        return response()->json([
            'success' => true,
            'message' => 'Menghapus Pengguna Berhasil!',
        ]);
    }
}
