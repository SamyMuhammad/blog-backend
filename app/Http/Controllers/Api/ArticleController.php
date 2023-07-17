<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use GuzzleHttp\Middleware;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use F9Web\ApiResponseHelpers;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Resources\ArticleListResource;
use App\Http\Resources\ArticleDetailsResource;

class ArticleController extends Controller
{
    use ApiResponseHelpers;

    public function __construct() {
        $this->middleware('auth:sanctum')->only(['myArticles', 'store', 'update', 'destroy']);
        $this->authorizeResource(Article::class, 'article');
    }

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

    public function myArticles(Request $request)
    {
        $articles = $request->user()->articles()->paginate(9);

        return ArticleListResource::collection($articles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request)
    {
        $data = $request->validated();

        $data['cover'] = $request->file('cover')->store('covers', 'public');

        $article = $request->user()->articles()->create($data);

        return $this->respondCreated(new ArticleDetailsResource($article));
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
        $data = $request->validated();

        if ($request->hasFile('cover')) {
            $data['cover'] = $request->file('cover')->store('covers', 'public');
        } else {
            unset($data['cover']);
        }

        $article->update($data);

        return $this->respondCreated(new ArticleDetailsResource($article));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Article $article): JsonResponse
    {
        $cover = $article->cover;
        $article->delete();

        // Delete cover image from file storage
        if (! Str::startsWith($cover, 'http') && Storage::disk('public')->exists($cover)) {
            Storage::disk('public')->delete($cover);
        }
        return $this->respondOk("Article has been deleted successfully!");
    }
}
