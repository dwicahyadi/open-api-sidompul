<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDompulChipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dompul_chips', function (Blueprint $table) {
            $table->id();
            $table->string('msisdn',15)->unique();
            $table->string('pin', 255)->nullable();
            $table->string('client_id', 255);
            $table->string('client_secret', 255);
            $table->string('access_token', 255)->nullable();
            $table->dateTime('token_expired_at',)->nullable();
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
        Schema::dropIfExists('dompul_chips');
    }
}
