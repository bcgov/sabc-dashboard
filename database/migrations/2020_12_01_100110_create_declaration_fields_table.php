<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeclarationFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('declaration_fields', function (Blueprint $table) {
            $table->id();

            $table->bigInteger('declaration_id')->unsigned();
            $table->foreign('declaration_id')->references('id')->on('declarations')->onDelete('cascade');

            $table->string('field_id');
            $table->string('field_label');
            $table->longText('field_value');
            $table->integer('field_weight');

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
        Schema::dropIfExists('declaration_fields');
    }
}
