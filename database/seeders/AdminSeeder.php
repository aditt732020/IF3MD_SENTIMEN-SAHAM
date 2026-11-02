<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder akun Administrator & Visitor (FR-01 & FR-02).
 * - Admin   : username 'adit'    | password 'adit732020'
 * - Visitor : username 'visitor' | password 'visitor123'
 */
class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::where('name', 'adit')->where('email', '!=', 'adit@stocksense.id')->delete();

        User::updateOrCreate(
            ['email' => 'adit@stocksense.id'],
            [
                'name'     => 'adit',
                'password' => Hash::make('adit732020'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'visitor@stocksense.id'],
            [
                'name'     => 'visitor',
                'password' => Hash::make('visitor123'),
                'role'     => 'visitor',
            ]
        );
    }
}
