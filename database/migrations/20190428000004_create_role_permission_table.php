<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateRolePermissionTable
{
    public function up(Schema $schema)
    {
        $schema->create('role_permission', function ($table) {
            $table->increments('id');
            $table->integer('role_id');
            $table->integer('permission_id');

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
            ;

            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
            ;
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('role_permission');
    }
}