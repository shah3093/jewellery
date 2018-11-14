<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopinformationsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('shopinformations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('about');
            $table->string('email');
            $table->string('phone');
            $table->string('address');
            $table->string('favicon');
            $table->string('logo');
            $table->text('google_map');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('shopinformations');
    }

}
