<?php

use Illuminate\Database\Schema\Builder as Schema;
use App\Models\User;
use Amber\Framework\Helpers\Hash;

class DatabaseSeeder
{
    public function seeds()
    {
        return [
        	'UserTableSeeder',
        ];
    }
}
