<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateRolesPermissionsTable
{
    public function up(Schema $schema)
    {
        $schema->create('roles_permissions', function ($table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->integer('permission_id');
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('roles_permissions');
    }
}
