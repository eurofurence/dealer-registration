<?php

namespace Database\Seeders;

use App\Models\TableType;
use Illuminate\Database\Seeder;

class TableTypeSeeder extends Seeder
{
    public function run()
    {
        TableType::firstOrCreate([
            "id" => 1,
        ], [
            "id" => 1,
            "seats" => 1,
            "spaces" => 1,
            "price" => 5000,
            "name" => "Half (65cm x 65cm)",
            "package" => "dealer-half",
        ]);
        TableType::firstOrCreate([
            "id" => 2,
        ], [
            "id" => 2,
            "seats" => 2,
            "spaces" => 1,
            "price" => 10000,
            "name" => "Full (130cm x 65cm) [recommended]",
            "package" => "dealer-full",
        ]);
        TableType::firstOrCreate([
            "id" => 3,
        ], [
            "id" => 3,
            "seats" => 3,
            "spaces" => 1,
            "price" => 15000,
            "name" => "Full Plus (195cm x 65cm)",
            "package" => "dealer-full",
        ]);
        TableType::firstOrCreate([
            "id" => 4,
        ], [
            "id" => 4,
            "seats" => 4,
            "spaces" => 1,
            "price" => 20000,
            "name" => "Double Length (260cm x 65cm)",
            "package" => "dealer-double",
        ]);
        TableType::firstOrCreate([
            "id" => 5,
        ], [
            "id" => 5,
            "seats" => 2,
            "spaces" => 1,
            "price" => 20000,
            "name" => "Double Width (130cm x 130cm)",
            "package" => "dealer-double",
        ]);
        TableType::firstOrCreate([
            "id" => 6,
        ], [
            "id" => 6,
            "seats" => 4,
            "spaces" => 1,
            "price" => 40000,
            "name" => "Quad (260cm x 130cm)",
            "package" => "dealer-quad",
        ]);


    }
}
