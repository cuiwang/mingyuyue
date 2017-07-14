<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDicitionariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dicitionaries', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            //
            $table->string('name')->comment('名字');
            $table->string('pinyin')->nullable()->comment('拼音');
            $table->string('bushou')->nullable()->comment('部首');
            $table->string('bihua')->nullable()->comment('笔画');
            $table->string('fanti')->nullable()->comment('繁体');
            $table->string('wuxin')->nullable()->comment('五行');
            $table->longText('jieshi')->nullable()->comment('解释');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dicitionaries');
    }
}
