<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Mail\CommentAddedOnEvent;
use App\Models\Comment;
use App\Models\Events;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

class CommentEventsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCommentRequest $request
     * @param Events $events
     * @return JsonResponse
     */
    public function store(StoreCommentRequest $request, Events $events): JsonResponse
    {
        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        $comment = Comment::factory([
            'content' => $validatedData['content']
        ])->make();

        $request->user()->comments()->save($comment);
        $events->comments()->save($comment);

        $mail = Mail::to($events->user()->get());
        $mail->send(new CommentAddedOnEvent($events, $comment));

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
