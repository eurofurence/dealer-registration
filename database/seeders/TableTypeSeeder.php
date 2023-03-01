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
            "name" => "Half (65cm x 65cm)",
        ]);
        TableType::firstOrCreate([
            "id" => 2,
        ], [
            "id" => 2,
            "seats" => 2,
            "name" => "Full (130cm x 65cm) [recommended]",
        ]);
        TableType::firstOrCreate([
            "id" => 3,
        ], [
            "id" => 3,
            "seats" => 4,
            "name" => "Double Length (260cm x 65cm)",
        ]);
        TableType::firstOrCreate([
            "id" => 4,
        ], [
            "id" => 4,
            "seats" => 2,
            "name" => "Double Width (130cm x 130cm)",
        ]);
        TableType::firstOrCreate([
            "id" => 5,
        ], [
            "id" => 5,
            "seats" => 4,
            "name" => "Quad (260cm x 130cm)",
        ]);


    }
}
