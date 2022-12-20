<?php

namespace Database\Factories;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class AppointmentFactory extends Factory
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
            'client_id' => rand(11,20),
            'consultant_id'=>rand(1,10),
            'appointment_date'=>fake()->date(),
            'appointment_start' => $startDate->addHours(rand(0, 3))->format('H:i'),
            'appointment_end' => $startDate->addHours(rand(4, 10))->format(('H:i'))
        ];
    }
}
