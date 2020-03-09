<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIndexForeignKeyForAdministrativeArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->index('province_id', 'province_id_index');
        });

        Schema::table('wards', function (Blueprint $table) {
            $table->index('district_id', 'district_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('districts', function (Blueprint $table) {
            $table->dropIndex('province_id_index');
        });

        Schema::table('wards', function (Blueprint $table) {
            $table->dropIndex('district_id_index');
        });
    }
}
