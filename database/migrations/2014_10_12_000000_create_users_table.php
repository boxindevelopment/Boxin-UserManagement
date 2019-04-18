<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name',120);
            $table->string('last_name',120)->nullable();
            $table->string('email',100)->unique();
            $table->string('phone',20)->nullable();
            $table->string('facebook_id',100)->nullable();
            $table->string('google_id',100)->nullable();
            $table->string('password',160);
            $table->text('remember_token')->nullable();
            $table->string('image', 225)->nullable();
            $table->tinyInteger('status')->default(2);$table->text('address')->nullable();
            $table->integer('roles_id')->unsigned()->default(1);            
            $table->timestamps();

            $table
                ->foreign('roles_id')->references('id')->on('roles')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
