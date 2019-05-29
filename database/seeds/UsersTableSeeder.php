<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\User;
use Amber\Framework\Helpers\Hash;

class UserTableSeeder
{
    public function run()
    {
        $user = new User();

        $user->name = 'Administrator';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('secret');

        $user->save();
    }
}
