<?php namespace PolloZen\RankPost\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateRanksTable extends Migration
{

    public function up()
    {
        Schema::create('pollozen_rankpost_ranks', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->float('star')->default(5.0);
            $table->integer('vote')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pollozen_rankpost_ranks');
    }

}
