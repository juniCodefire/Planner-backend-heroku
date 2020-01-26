<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspacecollaborateworkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspacecollaborateworkspaces', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('initiator_ws_id');
            $table->unsignedInteger('initiatee_ws_id');
            $table->timestamps();

            $table->foreign('initiator_ws_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('initiatee_ws_id')->references('id')->on('workspaces')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspacecollaborateworkspaces');
    }
}
