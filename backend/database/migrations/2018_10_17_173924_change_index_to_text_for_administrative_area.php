<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIndexToTextForAdministrativeArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropIndex('name_text');
            $table->index(['name' => 'text'], 'name_full_text');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropIndex('name_text');
            $table->index(['name' => 'text'], 'name_full_text');
        });

        Schema::table('wards', function (Blueprint $table) {
            $table->dropIndex('name_text');
            $table->index(['name' => 'text'], 'name_full_text');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('provinces', function (Blueprint $table) {
            $table->dropIndex('name_full_text');
            $table->index('name', 'name_text');
        });

        Schema::table('districts', function (Blueprint $table) {
            $table->dropIndex('name_full_text');
            $table->index('name', 'name_text');
        });

        Schema::table('wards', function (Blueprint $table) {
            $table->dropIndex('name_full_text');
            $table->index('name', 'name_text');
        });
    }
}
