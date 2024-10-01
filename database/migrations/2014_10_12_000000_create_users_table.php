<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            //$table->id();
            $table->bigIncrements('id');

            $table->bigInteger('uid');
            $table->string('name', 60);
            $table->string('password', 128);
            $table->string('email', 254)->nullable();
            //$table->string('mail', 254)->unique();
            //$table->timestamp('email_verified_at')->nullable();

            $table->integer('created')->default(0);
            $table->integer('access')->default(0);
            $table->integer('login')->default(0);
            $table->smallInteger('status')->default(0);
            $table->string('timezone')->nullable();
            $table->text('data')->nullable();
            $table->longText('saml')->nullable();

            $table->rememberToken();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP'));
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
