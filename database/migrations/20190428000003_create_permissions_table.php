<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreatePermissionsTable
{
    public function up(Schema $schema)
    {
        $schema->create('permissions', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('permissions');
    }
}
