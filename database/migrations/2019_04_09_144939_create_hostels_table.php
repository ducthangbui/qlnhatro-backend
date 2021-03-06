<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateHostelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hostels', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('userid');
            $table->integer('electricprice');
            $table->integer('waterprice');
            $table->integer('sanitationcost');
            $table->integer('securitycost');
            $table->integer('closedtime');
            $table->integer('status');
            $table->integer('price');
            $table->string('img');
            $table->integer('addid');
            $table->integer('views');
            $table->integer('haslandlords');
            $table->integer('floorarea');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('hostels');
    }
}
