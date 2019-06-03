<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\User::class, 5)
        ->create()
        ->each(function ($user) {
            $user->questions()
            ->saveMany(
                factory(App\Question::class, rand(1, 5))->make()
            )
            ->each(function ($question) {
                $question->answers()->saveMany(factory(App\Answer::class,rand(1,5))->make());
            });
        });
    }
}
