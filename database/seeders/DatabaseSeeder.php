<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');
        // \App\Models\User::factory(10)->create();
        for ($i = 1; $i <= 10; $i++) {
            DB::table('customers')->insert([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone_number' => '0821' . $faker->randomNumber(8),
                'address' => $faker->address
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            DB::table('suppliers')->insert([
                'name' => $faker->name,
                'email' => $faker->email,
                'phone_number' => '0821' . $faker->randomNumber(8),
                'address' => $faker->address
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            DB::table('categories')->insert([
                'name' => $faker->name,
                'image' => '1680653843_tes_jpg'
            ]);
        }

        for ($i = 1; $i <= 10; $i++) {
            DB::table('products')->insert([
                'name' => 'Product'.$i,
                'image' => '1680653843_tes_jpg',
                'stock' => '40',
                'price_forSale' => 10000,
                'price_fromSupplier' => 8000,
                'profit' => 2000,
                'category_id' => rand(1,4),
                'supplier_id' => rand(1,4)
            ]);
        }
    }
}
