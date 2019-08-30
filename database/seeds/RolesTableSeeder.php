<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\RoleProvider as Provider;
use Amber\Helpers\Hash;

class RolesTableSeeder
{
    public function run()
    {
        $provider = new Provider();

        $admin = $provider->new();
        $admin->name = 'Administrator';

        $user = $provider->new();
        $user->name = 'New User';

        $provider->insert($admin);
        $provider->insert($user);
    }
}
