<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Http\Requests\Events\EventRequest;
use App\Models\Events;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = Events::all();

        return response()->json([
            'success' => true,
            'data' => $events
        ]);
    }

    public function store(EventRequest $request): JsonResponse
    {
        Log::info('EventController@store called');
        Log::info('Request data:', $request->all());
        try {
            $validated = $request->validated();
            Log::info('Validated data:', $validated);

            $event = Events::create($validated);
            Log::info('Event created:', $event->toArray());

            return response()->json([
                'success' => true,
                'message' => 'Event created successfully',
                'data' => $event
            ], 201); // This should give 201 Created

        } catch (\Exception $e) {
            Log::error('Error in store method:', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function show(Events $event): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $event
        ]);
    }

    public function update(EventRequest $request, Events $event): JsonResponse
    {
        $event->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Event updated successfully',
            'data' => $event
        ]);
    }

    public function destroy(Events $event): JsonResponse
    {
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }
}
