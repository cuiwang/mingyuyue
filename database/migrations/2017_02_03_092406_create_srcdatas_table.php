<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrcdatasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srcdatas', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //
            $table->string('from')->nullable()->comment('来源');
            $table->string('by')->nullable()->comment('分类');
            $table->string('time')->nullable()->comment('朝代');
            $table->string('author')->nullable()->comment('作者');
            $table->string('name')->nullable()->comment('名称');
            $table->string('description')->nullable()->comment('简介');
            $table->text('content')->nullable()->comment('内容');
            $table->integer('done')->default(0)->comment('是否处理');
            $table->integer('click')->default(0)->comment('点击数');
            $table->integer('size')->default(0)->comment('個数');
            $table->integer('custom')->default(0)->comment('是否自定义');
            $table->string('imageurl')->nullable()->comment('封面图片');
            $table->tinyInteger('free')->default(0)->comment('免费');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srcdatas');
    }
}
