<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->has(Article::factory()->count(3))->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()
            ->count(29)
            ->has(
                Article::factory()
                    ->count(5)
            )
            ->create();
    }
}
