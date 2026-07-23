<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{

    public function run(): void
    {

        // disable FK
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        Menu::truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');


        Menu::insert([

            [
                'name'=>'India',
                'slug'=>'india',
                'type'=>'india',
                'order_seq'=>1,
                'is_active'=>1
            ],

            [
                'name'=>'International',
                'slug'=>'international',
                'type'=>'international',
                'order_seq'=>2,
                'is_active'=>1
            ],

            [
                'name'=>'Holidays',
                'slug'=>'holidays',
                'type'=>'holiday',
                'order_seq'=>3,
                'is_active'=>1
            ],

            [
                'name'=>'Luxury',
                'slug'=>'luxury',
                'type'=>'luxury',
                'order_seq'=>4,
                'is_active'=>1
            ],

            [
                'name'=>'Car Rental',
                'slug'=>'car-rental',
                'type'=>'car',
                'order_seq'=>5,
                'is_active'=>1
            ],

            [
                'name'=>'Indian DMC',
                'slug'=>'indian-dmc',
                'type'=>'dmc',
                'order_seq'=>6,
                'is_active'=>1
            ],

            [
                'name'=>'Contact',
                'slug'=>'contact',
                'type'=>'contact',
                'order_seq'=>7,
                'is_active'=>1
            ],

        ]);

    }

}