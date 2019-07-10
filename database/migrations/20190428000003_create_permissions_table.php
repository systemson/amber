<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreatePermissionsTable
{
    public function up(Schema $schema)
    {
        $schema->create('permissions', function ($table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('permissions');
    }
}
