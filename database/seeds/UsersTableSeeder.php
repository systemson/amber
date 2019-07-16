<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\UserProvider;
use Amber\Helpers\Hash;

class UserTableSeeder
{
    public function run()
    {
        $provider = new UserProvider();
        $user = $provider->new();

        $user->name = 'Administrator';
        $user->email = 'admin@admin.com';
        $user->password = Hash::make('secret');

        $provider->insert($user);

        $user = $provider->new();

        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password = Hash::make('secret');

        $provider->insert($user);
    }
}
