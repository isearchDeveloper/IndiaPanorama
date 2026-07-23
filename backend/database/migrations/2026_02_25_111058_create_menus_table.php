<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

public function up(){

Schema::create('menus',function(Blueprint $table){

$table->id();

$table->string('name');

$table->string('slug')->unique();

$table->string('type')->nullable();

/*
india
international
holiday
luxury
car
contact
*/

$table->integer('order_seq')->default(0);

$table->boolean('is_active')->default(1);

$table->timestamps();

});

}

public function down(){

Schema::dropIfExists('menus');

}

};