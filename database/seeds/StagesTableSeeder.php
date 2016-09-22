<?php

use Illuminate\Database\Seeder;
use App\Stage;

class StagesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Stage::create(['name' => 'Today']);
        Stage::create(['name' => 'WIP']);
        Stage::create(['name' => 'KIV']);
        Stage::create(['name' => 'Done']);
    }
}
