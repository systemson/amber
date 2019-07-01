<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\User;
use Amber\Helpers\Hash;

class UserTableSeeder
{
    public function run()
    {
        $user = new User();

        $user->name = 'Administrator';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('secret');

        $user->save();

        $user = new User();

        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password = Hash::make('secret');

        $user->save();
    }
}
