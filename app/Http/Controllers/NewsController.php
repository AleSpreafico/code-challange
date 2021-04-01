<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreNewsRequest;
use App\Http\Requests\UpdateNewsRequest;
use App\Models\News;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

class NewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(): JsonResponse
    {
        return response()->json(
            News::all()
                ->load('comments')
                ->where('created_at', '>=', Carbon::today())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreNewsRequest $request
     * @return JsonResponse
     */
    public function store(StoreNewsRequest $request): JsonResponse
    {
        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        $news = News::factory([
            'title' => $validatedData['title'],
            'content' => $validatedData['content']
        ])->make();

        /** @var User $user */
        $user = $request->user();

        $user->news()->save($news);

        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param News $news
     * @return JsonResponse
     */
    public function show(News $news): JsonResponse
    {
        return response()->json($news->load('comments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateNewsRequest $request
     * @param News $news
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UpdateNewsRequest $request, News $news): JsonResponse
    {
        $this->authorize('update', $news);

        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        if (Arr::exists($validatedData, 'title')) {
            $news->update([
                'title' => $validatedData['title'],
            ]);
        }

        if (Arr::exists($validatedData, 'content')) {
            $news->update([
               'content' => $validatedData['content'],
            ]);
        }

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param News $news
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(News $news): JsonResponse
    {
        $this->authorize('delete', $news);

        $news->delete();

        return response()->json();
    }
}
