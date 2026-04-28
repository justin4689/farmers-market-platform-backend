<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'       => 'Ibrahim Admin',
            'email'      => 'admin@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'admin',
            'created_by' => null,
        ]);

        $supervisor1 = User::create([
            'name'       => 'Fatou Supervisor',
            'email'      => 'supervisor1@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'supervisor',
            'created_by' => $admin->id,
        ]);

        $supervisor2 = User::create([
            'name'       => 'Moussa Supervisor',
            'email'      => 'supervisor2@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'supervisor',
            'created_by' => $admin->id,
        ]);

        User::create([
            'name'       => 'Amara Operator',
            'email'      => 'operator1@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'operator',
            'created_by' => $supervisor1->id,
        ]);

        User::create([
            'name'       => 'Seydou Operator',
            'email'      => 'operator2@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'operator',
            'created_by' => $supervisor1->id,
        ]);

        User::create([
            'name'       => 'Mariam Operator',
            'email'      => 'operator3@xpertbot.com',
            'password'   => Hash::make('password'),
            'role'       => 'operator',
            'created_by' => $supervisor2->id,
        ]);
    }
}
