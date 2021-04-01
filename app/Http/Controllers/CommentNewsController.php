<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\News;
use Illuminate\Http\JsonResponse;

class CommentNewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCommentRequest $request
     * @param News $news
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, News $news): JsonResponse
    {
        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        $comment = Comment::factory([
            'content' => $validatedData['content']
        ])->make();

        $request->user()->comments()->save($comment);
        $news->comments()->save($comment);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Comment $comment
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Comment $comment): JsonResponse
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->json();
    }
}
