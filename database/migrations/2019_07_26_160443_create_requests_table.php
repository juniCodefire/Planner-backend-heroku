<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requestinvites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('requestee_id');
            $table->unsignedInteger('requester_id');
            $table->unsignedInteger('workspace_id');
            $table->timestamps();

            $table->foreign('requestee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requests');
    }
}
