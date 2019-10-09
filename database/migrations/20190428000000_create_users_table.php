<?php

use Illuminate\Database\Schema\Builder as Schema;

class CreateUsersTable
{
    public function up(Schema $schema)
    {
        $schema->create('users', function ($table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->text('description')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->string('remember_token')->nullable();
            $table->timestamps();
        });
    }

    public function down(Schema $schema)
    {
        $schema->dropIfExists('users');
    }
}
