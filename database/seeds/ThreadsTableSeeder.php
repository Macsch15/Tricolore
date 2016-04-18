<?php

use Illuminate\Database\Seeder;

class ThreadsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('threads')->insert([
            'title' => 'Welcome to Tricolore!',
            'created_at' => Carbon\Carbon::now(),
            'member_username' => 'Tricolore',
            'member_id' => 0,
            'forum_id' => 0,
            'flag' => 'closed',
            'visitor' => '0.0.0.0'
        ]);
    }
}