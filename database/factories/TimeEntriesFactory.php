<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeEntries>
 */
class TimeEntriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public static function createClockOut(){
        $randResult = random_int(0,1);
        if($randResult===1){
            return fake()->dateTime();
        }else{
            return null;
        }
    }

    public function definition(): array
    {
        return [
            "user_id"=>User::all()->random()->id,
            "clock_in_at"=>fake()->dateTime(),
            "clock_out_at"=>$this->createClockOut(),
        ];
    }
}
