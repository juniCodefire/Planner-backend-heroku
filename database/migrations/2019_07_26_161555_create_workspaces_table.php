<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkspacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workspaces', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('owner_id');
            $tbale->unsignedInteger('company_id')->default(null);
            $table->string('title');
            $table->string('unique_name');
            $table->string('role')->default('admin');
            $table->string('wallpaper')->default(null);
            $table->string('description')->default('Description can help improve clarity of workspace actual purpose!');
            $table->enum('status', array('Public','Private'));
            $table->timestamps();

            $table->foreign('owner_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workspaces');
    }
}
