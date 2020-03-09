<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeIndexToTextForLocationType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('location_types', function (Blueprint $table) {
            $table->dropIndex('name_text');
            $table->index('slug', 'slug_index');
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
        Schema::table('location_types', function (Blueprint $table) {
            $table->dropIndex('name_full_text');
            $table->dropIndex('slug_index');
            $table->index('name', 'name_text');
        });
    }
}
