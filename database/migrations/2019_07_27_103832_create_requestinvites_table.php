->default(null)<?php

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
            $table->unsignedInteger('requestee_id')->nullable();
            $table->unsignedInteger('requester_id')->nullable();
            $table->unsignedInteger('workspace_id')->nullable();
            $table->unsignedInteger('company_id')->nullable();
            $table->timestamps();

            $table->foreign('requestee_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('workspace_id')->references('id')->on('workspaces')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
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
