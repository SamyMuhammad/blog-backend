<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usersIds = User::pluck('id');
        foreach (Article::all() as $article) {
            for ($i = 1; $i <= 10; $i++) {
                $randomUserId = $usersIds->random();
                Comment::create([
                    'user_id' => $randomUserId,
                    'article_id' => $article->id,
                    'body' => fake()->text(),
                ]);
            }
        }
    }
}
