<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexLocationIdOnAddLocInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('add_loc_info', function (Blueprint $table) {
            $table->index('location_id', 'location_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('add_loc_info', function (Blueprint $collection) {
            $collection->dropIndex('location_id_index');
        });
    }
}
