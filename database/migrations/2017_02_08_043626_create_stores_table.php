<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //
            $table->integer('name_id')->unsigned()->comment('词来源');
            $table->foreign('name_id')->references('id')->on('names');

            $table->integer('user_id')->unsigned()->comment('所属用户');
            $table->foreign('user_id')->references('id')->on('users');

            $table->string('first_name')->comment('姓');
            $table->string('name')->comment('名称');

            $table->string('description')->nullable()->comment('简介');

            $table->string('score')->nullable()->comment('得分');

            $table->tinyInteger('star')->default(0)->comment('星级');
            $table->tinyInteger('deleted')->default(0)->comment('软删除');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}
