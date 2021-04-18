<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => "admin1",
            'email' => 'admin1@gmail.com',
            'password' => Hash::make('password'),
            'admin' => 'true',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('users')->insert([
            'name' => "admin2",
            'email' => 'admin2@gmail.com',
            'password' => Hash::make('password'),
            'admin' => 'true',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // DB::table('personal_access_tokens')->insert([
        //     'tokenable_type' => "App\Models\User",
        //     'tokenable_id' => "1",
        //     'name' => 'admin1token',
        //     'token' =>Hash::make('1|vBBBaLJ5jmTZ5XhqglnUzouv3b7W71qTsYadmin1'),
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now(),
        // ]);
        // DB::table('personal_access_tokens')->insert([
        //     'tokenable_type' => "App\Models\User",
        //     'tokenable_id' => "2",
        //     'name' => 'admin2token',
        //     'token' =>Hash::make('2|vBBBaLJ5jmTZ5XhqglnUzouv3b7W71qTsYadmin2'),
        //     'created_at' => Carbon::now(),
        //     'updated_at' => Carbon::now(),
        // ]);
    }
}
