<?php

namespace App\Http\Controllers;

use App\Http\Resources\SpruceNoteResource;
use App\Models\Patient;
use App\Models\SpruceNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SpruceNoteController extends Controller
{

    public function syncSpruceNotes($contactId)
    {
        $response = Http::withToken(env('SPRUCE_API_KEY'))
            ->get("https://api.sprucehealth.com/v1/contacts/{$contactId}/conversations");

        $patient = Patient::where('external_contact_id', $contactId)->first();

        if (!$patient) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $syncedNotes = [];

        $conversations = collect($response->json()['conversations'] ?? [])
            ->filter(fn($conv) => $conv['type'] === 'note');

        foreach ($conversations as $conversation) {
            $convId = $conversation['id'];
            $title = $conversation['title'] ?? null;
            $subtitle = $conversation['subtitle'] ?? null;
            $lastMessageAt = $conversation['lastMessageAt'] ?? null;

            $itemsResponse = Http::withToken(env('SPRUCE_API_KEY'))
                ->get("https://api.sprucehealth.com/v1/conversations/{$convId}/items");

            foreach ($itemsResponse->json()['conversationItems'] ?? [] as $item) {
                $text = $item['text'] ?? '';
                $attachments = $item['attachments'] ?? [];
                $author = $item['author']['displayName'] ?? 'Unknown';
                $itemId = $item['id'] ?? null;

                if (!$itemId) continue;

                $note = SpruceNote::updateOrCreate(
                    ['conversation_item_id' => $itemId],
                    [
                        'patient_id' => $patient->id,
                        'conversation_id' => $convId,
                        'title' => $title,
                        'subtitle' => $subtitle,
                        'lastMessageAt' => $lastMessageAt,
                        'note_text' => $text,
                        'author_name' => $author,
                        'attachments' => json_encode($attachments),
                    ]
                );

                $syncedNotes[] = $note;
            }
        }
        return $this->sendResponse(
            SpruceNoteResource::collection(collect($syncedNotes)),
            'Spruce notes synced successfully.'
        );
    }
}
