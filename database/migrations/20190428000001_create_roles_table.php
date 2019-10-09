<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateRolesTable
{
    public function up(Schema $schema)
    {
        $schema->create('roles', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('roles');
    }
}
