<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\UserProvider;
use Amber\Helpers\Hash;

class UserTableSeeder
{
    public function run()
    {
        $provider = new UserProvider();

        $admin = $provider->new();
        $admin->name = 'Administrator';
        $admin->email = 'admin@admin.com';
        $admin->password = Hash::make('secret');

        $user = $provider->new();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password = Hash::make('secret');


        $provider->insert($admin);
        $provider->insert($user);
    }
}
