<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEventsRequest;
use App\Models\Events;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class EventsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $queryParameters = $request->all();
        if (Arr::exists($queryParameters, 'dateOfEvent')) {
            $dayOfEvent = Carbon::parse($queryParameters['dateOfEvent']);
            return response()->json(
                Events::all()
                    ->load('comments')
                    ->where('valid_from', '<=', $dayOfEvent)
                    ->where('valid_to', '>=', $dayOfEvent)
            );
        }

        return response()->json(
            Events::all()
                ->load('comments')
                ->where('valid_from', '>=', Carbon::today())
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreEventsRequest  $request
     * @return JsonResponse
     */
    public function store(StoreEventsRequest $request): JsonResponse
    {
        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        $event = Events::factory([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'valid_from' => $validatedData['valid_from'],
            'valid_to' => $validatedData['valid_to'],
            'gps_lat' => $validatedData['gps_lat'],
            'gps_lng' => $validatedData['gps_lng'],
        ])->make();

        /** @var User $user */
        $user = $request->user();

        $user->events()->save($event);

        return response()->json();
    }

    /**
     * Display the specified resource.
     *
     * @param Events $event
     * @return JsonResponse
     */
    public function show(Events $event): JsonResponse
    {
        return response()->json($event->load('comments'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param StoreEventsRequest $request
     * @param Events $event
     * @return JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(StoreEventsRequest $request, Events $event): JsonResponse
    {
        $this->authorize('update', $event);

        /** @var array<string> $validatedData */
        $validatedData = $request->validated();

        $event->update([
            'title' => $validatedData['title'],
            'content' => $validatedData['content'],
            'valid_from' => $validatedData['valid_from'],
            'valid_to' => $validatedData['valid_to'],
            'gps_lat' => $validatedData['gps_lat'],
            'gps_lng' => $validatedData['gps_lng'],
        ]);

        return response()->json();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Events $event
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Events $event): JsonResponse
    {
        $this->authorize('delete', $event);

        $event->delete();

        return response()->json();
    }
}
