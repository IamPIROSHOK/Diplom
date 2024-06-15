<?php

namespace Database\Factories;

use App\Models\Master;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Master>
 */
class MasterFactory extends Factory
{
    protected $model = Master::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'description' => $this->faker->text(20),
        ];
    }
}
