<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAssetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('table_id');
            $table->string('table_name');
            $table->string('caption');
            $table->string('file_name');
            $table->string('details');
            $table->enum('type',['IMAGE','FILE','ICON','TOPICIMAGE','BACKGROUNDIMAGE','LOGO']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('assets');
    }
}
