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
         $table->integer('from_userid')->nullable();
          $table->tinyInteger('status')
                ->default(0)
                ->comment('0=loss or deduct, 1=win or add');
                $table->tinyInteger('is_xp_or_coin')
                ->default(0)
                ->comment('0=coin, 1=xp');
        $table->tinyInteger('game_type')
                ->default(1)
                ->comment('1=Reward points, 2=Google ads, 3=Rain On, 4=spinner wheel, 5=game win/loss,6=welcome bonus, 7=social login');
       
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
