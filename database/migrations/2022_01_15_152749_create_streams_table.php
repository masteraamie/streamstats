<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('channel_name')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('game_id')->nullable();
            $table->string('game_name')->nullable();
            $table->bigInteger('viewer_count')->nullable();
            $table->string('started_at')->nullable();
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
        Schema::dropIfExists('streams');
    }
}
