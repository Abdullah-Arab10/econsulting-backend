<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Consultant>
 */
class ConsultantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startDate = Carbon::createFromFormat('H:i', '9:00');

        return [
            'user_id' => User::factory(1)->create()->first(),
            'avgRating' => rand(0, 5),
            'skill' => rand(0, 6),
            'bio' => fake()->paragraph(),
            'shiftStart' => $startDate->addHours(rand(0, 3))->format('H:i'),
            'shiftEnd' => $startDate->addHours(rand(4, 10))->format(('H:i')),
            'appointment_cost'=>rand(10,1000)
        ];
    }
}
