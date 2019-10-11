<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\UserProvider as Provider;
use Amber\Helpers\Crypto\Hash;

class UserTableSeeder
{
    public function run()
    {
        $provider = new Provider();

        $admin = $provider->new();
        $admin->name = 'Administrator';
        $admin->email = 'admin@admin.com';
        $admin->description = 'The superadmin user.';
        $admin->password = Hash::make('secret');

        $user = $provider->new();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->description = 'Just a regular user.';
        $user->password = Hash::make('secret');

        $provider->insert($admin);
        $provider->insert($user);
    }
}
