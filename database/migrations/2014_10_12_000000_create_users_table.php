<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('firstName')->nullable();//姓
            $table->string('lastName')->nullable();//名
            $table->timestamps();
            //微信返回
            $table->string('nickName')->nullable();
            $table->string('gender')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('avatarUrl')->nullable();
            $table->string('country')->nullable();
            $table->string('lastposition')->nullable();
            $table->string('sessionKey')->index();

            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
