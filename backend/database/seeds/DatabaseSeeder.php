<?php

use Illuminate\Database\Seeder;
use App\Modules\User\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'email' => 'viettrangha@gmail.com',
            'password' => 'aqotrjpm',
            'name' => 'Mason Ha',
            'role_id' => config('constants.USER.ROLE.ADMIN'),
        ]);
    }
}
