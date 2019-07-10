<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateRolesTable
{
    public function up(Schema $schema)
    {
        $schema->create('roles', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('roles');
    }
}
