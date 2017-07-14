<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('names', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //
            $table->integer('from')->unsigned()->comment('词来源');
            $table->foreign('from')->references('id')->on('srcdatas');

            $table->integer('type')->default(2)->unsigned()->comment('2或者3词');
            $table->string('name')->comment('名称');
            $table->string('description')->nullable()->comment('简介');
            //2017-05-01 22:52:56 添加一个字段
            $table->string('by')->nullable()->comment('书名作者');
            $table->string('from_name')->nullable()->comment('来源名字');
            $table->integer('loves')->default(0)->comment('收藏次数');
            $table->integer('views')->default(0)->comment('查看次数');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('names');
    }
}
