<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Ping>
 */
class PingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $code = array("200", "301", "302", "400", "401", "403", "500");

        return [
            'id' => Str::uuid()->toString(),
            'monitor_id' => "7ac5bada-823e-4d31-bfab-c1436a4fbc9d",
            'response_code' => $code[array_rand($code)],
            'response_time' => random_int(15, 1300),
        ];
    }
}
