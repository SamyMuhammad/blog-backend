<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleDetailsResource;
use App\Http\Resources\ArticleListResource;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use ApiResponseHelpers;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return $request->boolean('featured') ? $this->featuredArticles() : $this->allArticles();
    }

    public function allArticles()
    {
        $articles = Article::paginate(9);

        return ArticleListResource::collection($articles);
    }

    public function featuredArticles(): JsonResponse
    {
        $articles = Article::inRandomOrder()->limit(3)->get();

        return $this->respondWithSuccess([
            'data' => ArticleListResource::collection($articles)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article): JsonResponse
    {
        return $this->respondWithSuccess([
            'data' => new ArticleDetailsResource($article)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request, Article $article)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): JsonResponse
    {
        $article->delete();
        // TODO: Delete Cover Image
        return $this->respondOk("Article has been deleted successfully!");
    }
}
