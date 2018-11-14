<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductCategoriesTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::table('product_categories', function (Blueprint $table) {
            $table->string('slug')->unique();
            $table->string('meta_description');
            $table->string('meta_keywords');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        //
        Schema::table('product_categories', function($table) {
            $table->dropColumn('slug');
            $table->dropColumn('meta_description');
            $table->dropColumn('meta_keywords');
        });
    }

}
