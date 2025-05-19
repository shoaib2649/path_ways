<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Exception;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::with('provider')->get();
            return $this->sendResponse(EventResource::collection($events), 'Events retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError('Failed to retrieve events.', ['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {

        // dd($request->all());
        try {
            $event = Event::create([
                'provider_id' => $request->provider_id,
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'status' => $request->status ?? 'active',
            ]);

            return $this->sendResponse(new EventResource($event), 'Event created successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to create event.', ['error' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $event = Event::findOrFail($id);
            return $this->sendResponse(new EventResource($event), 'Event retrieved successfully!');
        } catch (Exception $e) {
            return $this->sendError('Event not found.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $event->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'location' => $request->location,
                'status' => $request->status ?? $event->status,
            ]);

            return $this->sendResponse(new EventResource($event), 'Event updated successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to update event.', ['error' => $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->delete();

            return $this->sendResponse([], 'Event deleted successfully!');
        } catch (Exception $e) {
            return $this->sendError('Failed to delete event.', ['error' => $e->getMessage()]);
        }
    }
}
