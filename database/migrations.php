<?php

use Illuminate\Database\Capsule\Manager;

Manager::schema()->drop('users');

Manager::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('username', 50);
            $table->string('name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('email', 100);
            $table->string('password', 255);
            $table->smallInteger('status')->default(1);
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_password_change')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
});
