<?php

namespace Database\Factories;

use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Schedule>
 */
class ScheduleFactory extends Factory
{
    protected $model = Schedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $start_time = $this->faker->time($format = 'H:i:s', $max = 'now');
        $end_time = date('H:i:s', strtotime($start_time) + rand(1, 4) * 3600); // Добавляем случайное количество часов к началу

        return [
            'master_id' => \App\Models\Master::factory(),
            'date' => $this->faker->dateTimeBetween('-1 month', '+1 month')->format('Y-m-d'),
            'start_time' => $start_time,
            'end_time' => $end_time,
        ];
    }
}
