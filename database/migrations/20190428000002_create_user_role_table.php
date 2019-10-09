<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateUserRoleTable
{
    public function up(Schema $schema)
    {
        $schema->create('user_role', function ($table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
            ;

            $table->foreign('role_id')
                ->references('id')
                ->on('roles')
            ;
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('user_role');
    }
}
