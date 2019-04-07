<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('goal_id');
            $table->integer('owner_id');
            $table->integer('assigned_id')->nullable();
            $table->string('task_title');
            $table->string('description');
            $table->string('begin_time');
            $table->string('begin_date');
            $table->string('due_time');
            $table->string('due_date');
            $table->string('reminder');
            $table->boolean('task_status')->default(false);
            $table->timestamps();

            $table->foreign('goal_id')->references('id')->on('goals')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
