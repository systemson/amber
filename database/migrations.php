<?php

use Illuminate\Database\Capsule\Manager;
use App\Models\User;
use Amber\Framework\Helpers\Hash;

Manager::schema()->dropIfExists('users');

Manager::schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('email', 100);
            $table->string('password', 255);
            $table->smallInteger('status')->default(1);
            $table->string('image', 255)->nullable();
            $table->text('description')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
});

$user = new User();

$user->name = 'Administrator';
$user->email = 'admin@admin.com';
$user->password = Hash::make('1234');
$user->save();
