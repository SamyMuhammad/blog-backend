<?php

namespace App\Http\Controllers\Api;

use App\Models\Article;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use F9Web\ApiResponseHelpers;

class CommentController extends Controller
{
    use ApiResponseHelpers;

    public function __construct()
    {
        $this->authorizeResource(Comment::class, 'comment');
    }    

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request, Article $article)
    {
        $article->comments()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        return CommentResource::collection($article->comments()->latest()->limit(40)->get()->sortBy('created_at'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        $comment->update(['body' => $request->body]);
        return $this->respondOk("Comment has been updated successfully!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
        return $this->respondOk("Comment has been deleted successfully!");
    }
}
