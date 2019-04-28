<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateUsersRolesTable
{
    public function up(Schema $schema)
    {
        $schema->create('users_roles', function ($table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('role_id');
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('users_roles');
    }
}
