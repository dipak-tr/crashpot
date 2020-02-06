<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->tinyInteger('is_active')->comment = '0 => No, 1 => Yes';
            $table->string('user_type')->nullable();
            $table->string('IMEI')->nullable();
            $table->tinyInteger('social_media_type')->comment = '0 => Guest, 1 => Facebook, 2 => Gmail';
            $table->string('social_media_id')->nullable();
            $table->tinyInteger('device_type')->default('1')->comment = '1 => Android, 2 => IOS';
            $table->string('device_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
