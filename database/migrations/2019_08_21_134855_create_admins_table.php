<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->default('plannrradmin');
            $table->string('email')->unique()->default('plannrradmin@plannrr.com');
            $table->string('wallpaper')->default('user.jpg');
            $table->string('password');
            $table->string('verify_code', 80)
            ->unique()
            ->nullable()
            ->default(null);
            $table->string('api_token', 80)
            ->unique()
            ->nullable()
            ->default(null);
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
        Schema::dropIfExists('admins');
    }
}
