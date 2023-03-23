<?php

use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\User::create([
            'name' => 'Itizaz Ur Rehman',
            'email' => 'itizaz.rehman@akdn.org',
            'password' => '12345678',
            'confirm' => '12345678'
        ]);
    }
}
