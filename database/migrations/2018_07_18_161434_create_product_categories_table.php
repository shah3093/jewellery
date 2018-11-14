<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductCategoriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->integer('parent_id');
            $table->string('name');
            $table->string('details');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('product_categories');
    }

}
