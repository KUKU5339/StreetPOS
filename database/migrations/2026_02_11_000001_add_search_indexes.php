<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->index(['user_id', 'name']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->index('product_id');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'name']);
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });
    }
};
