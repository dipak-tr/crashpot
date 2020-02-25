<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Usercoins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
          Schema::create('usercoins', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id');
        $table->integer('coins');
        $table->tinyInteger('game_type')
                ->default(1)
                ->comment('1=Reward points, 2=Google ads, 3=Rain On, 4=spinner wheel, 5=game win/loss');
        $table->dateTime('created_at');
        
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
