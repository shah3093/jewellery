<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductsTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('product_category_id')->unsigned();
            $table->foreign('product_category_id')->references('id')->on('product_categories')
                    ->onDelete("CASCADE")->onUpdate("CASCADE");

            $table->string("slug")->unique();
            $table->string("name");
            $table->integer("status");
            $table->text("details");
            $table->text("shortDetails");
            $table->double("price");
            $table->integer('quantity');
            $table->string("meta_description");
            $table->string("meta_keywords");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('products');
    }

}
