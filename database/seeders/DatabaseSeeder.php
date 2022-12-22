<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com'
        ]);

        
        DB::table('monitors')->insert([
            'id' => "7ac5bada-823e-4d31-bfab-c1436a4fbc9d",
            'name' => "Google",
            'address' => "https://google.com",
            'key' => random_int(100000000, 999999999),
        ]);

        DB::table('pings')->insert([
            'id' => "6gj5bada-823e-4d31-bfab-p9962a8gfd2a",
            'monitor_id' => "7ac5bada-823e-4d31-bfab-c1436a4fbc9d",
            'response_code' => 200,
            'response_time' => 15,
        ]);

        
    }
}
