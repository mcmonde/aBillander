<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCustomerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('customer_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

//            $table->string('home_page', 128)->nullable();       // Redirect after login to route_home
            $table->string('firstname', 32)->nullable();
            $table->string('lastname', 32)->nullable();
//            $table->string('timezone', 32)->nullable();

            $table->rememberToken();

            $table->tinyInteger('active')->default(1);

            $table->integer('language_id')->unsigned()->nullable(false); 

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
        Schema::dropIfExists('customer_users');
    }
}
