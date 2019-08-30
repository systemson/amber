<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\UserRolePivot as Provider;
use Amber\Helpers\Hash;

class UsersRolesTableSeeder
{
    public function run()
    {
        $provider = new Provider();

        $item = $provider->new();
        $item->user_id = 1;
        $item->role_id = 1;

        $provider->insert($item);

        $item = $provider->new();
        $item->user_id = 1;
        $item->role_id = 2;

        $provider->insert($item);
    }
}
