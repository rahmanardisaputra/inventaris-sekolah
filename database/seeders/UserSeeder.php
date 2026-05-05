<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Penjaga Sekolah',
                'email' => 'penjaga@sekolah.sch.id',
                'password' => Hash::make('password'),
                'role' => 'penjaga',
            ],
            [
                'name' => 'Staff Inventaris',
                'email' => 'staff@sekolah.sch.id',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ],
            [
                'name' => 'Waka Sarpras',
                'email' => 'waka@sekolah.sch.id',
                'password' => Hash::make('password'),
                'role' => 'waka',
            ],
        ]);
    }
}