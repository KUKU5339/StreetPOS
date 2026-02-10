<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->nullable()->constrained()->cascadeOnDelete();
        });

        // Update existing records to belong to the first user
        $firstUserId = \DB::table('users')->first()->id ?? 1;
        \DB::table('products')->update(['user_id' => $firstUserId]);
        \DB::table('sales')->update(['user_id' => $firstUserId]);

        // Now make the columns required
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable(false)->change();
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
