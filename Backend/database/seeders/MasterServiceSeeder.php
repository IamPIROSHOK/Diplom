<?php

namespace Database\Seeders;

use App\Models\Master;
use App\Models\Service;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $masters = Master::all();
        $services = Service::all();

        foreach ($masters as $master) {
            $servicesToAssign = $services->random(rand(1, 5)); // Присваиваем случайное количество услуг каждому мастеру
            $master->services()->attach($servicesToAssign);
        }
    }
}
