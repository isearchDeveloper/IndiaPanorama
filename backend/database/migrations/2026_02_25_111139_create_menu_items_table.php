<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

public function up(){

Schema::create('menu_items',function(Blueprint $table){

$table->id();

$table->unsignedBigInteger('menu_id');

$table->unsignedBigInteger('parent_id')

->nullable();

/*
NULL = Region

parent_id = State

parent_id = City
*/

$table->string('name');

$table->string('slug')->nullable();

$table->string('level')->nullable();

/*
region
state
city
continent
country
location
*/

$table->integer('order_seq')->default(0);

$table->boolean('is_active')->default(1);

$table->timestamps();


$table->foreign('menu_id')

->references('id')

->on('menus')

->cascadeOnDelete();


$table->foreign('parent_id')

->references('id')

->on('menu_items')

->cascadeOnDelete();

});

}

public function down(){

Schema::dropIfExists('menu_items');

}

};