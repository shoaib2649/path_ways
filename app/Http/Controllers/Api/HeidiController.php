<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\SftpService;
use Illuminate\Support\Facades\Auth;
use App\Models\UploadFile;
use App\Models\Transcribe;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Models\SessionData;

class HeidiController extends Controller
{   
    // SFTP
    protected $sftpService;

    public function __construct(SftpService $sftpService)
    {
        $this->sftpService = $sftpService;
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
            'patient_id' => 'required|exists:patients,patient_id', // Ensure patient exists
        ]);

        // Get authenticated user ID from token
        $userId = Auth::id();
        $patientId = $request->input('patient_id');

        // Get uploaded file
        $uploadedFile = $request->file('file');

        // Get the original file name
        $originalFileName = $uploadedFile->getClientOriginalName();

        // Generate uploaded file name (original name + extension + timestamp)
        $extension = $uploadedFile->getClientOriginalExtension();
        $baseFileName = pathinfo($originalFileName, PATHINFO_FILENAME);
        $uploadedName = "{$baseFileName}_".time().".{$extension}";

        // Get temporary file path
        $localFile = $uploadedFile->getPathname();

        // Upload file via SFTP
        $uploadSuccess = $this->sftpService->uploadFile($localFile, $uploadedName);

        if ($uploadSuccess) {
            // Store file details in the database
            UploadFile::create([
                'user_id' => $userId,
                'patient_id' => $patientId,
                'file_name' => $originalFileName,
                'uploaded_name' => $uploadedName,
            ]);

            return response()->json([
                'message' => 'File uploaded successfully!',
                'filename' => $uploadedName,
                'patient_id' => $patientId,
                'user_id' => $userId,
            ]);
        } else {
            return response()->json(['message' => 'File upload failed!'], 500);
        }
    }
    
    public function chatTemplate(Request $request)
    {
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/chat_template/";

        $response = Http::post($url, [
            "user_id" => $request->user_id,
            "user_answer" => $request->user_answer
        ]);

        return $response->json();
    }

    public function getTemplate(Request $request)
    {
        // Get the authenticated user from the token
        $user = Auth::user();

        // Check if the user is authenticated
        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized. Invalid or missing token.',
            ], 401);
        }

        // Get the user_id from the authenticated user
        $userId = $user->id;

        // Query the templates table to get all templates for this user_id
        $templates = DB::table('templates')
            ->where('user_id', $userId)
            ->get(); // Get all rows matching the user_id

        // Check if any templates were found
        if ($templates->isEmpty()) {
            return response()->json([
                'status' => 'error',
                'message' => 'No templates found for this user.',
            ], 404);
        }

        // Return all templates in the JSON response
        return response()->json([
            'templates' => $templates,
        ], 200);
    }
    
    public function getSoap(Request $request)
    {
       
        // Store data in session_data table
        $sessionData = SessionData::create([
            "user_id" => $request->user_id,
            "patient_id" => $request->patient_id,
            "text_data" => $request->text_data,
            "audio_file_name" => $request->audio_file_name, // Can be null
        ]);

        // Define the SOAP API URL
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/soap/";

        // Send request to SOAP API
        $response = Http::post($url, [
            "user_id" => $request->user_id,
            "patient_id" => $request->patient_id,
            "template_id" => $request->template_id,
            "template_length" => $request->template_length
        ]);

        return response()->json([
            "message" => "Data stored successfully and SOAP API called.",
            "session_data" => $sessionData,
            "soap_response" => $response->json(),
        ]);
    }

    public function nutrition(Request $request)
    {
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/nutrition/";

        $response = Http::post($url, [
            "user_id" => $request->user_id,
            "patient_id" => $request->patient_id,
            "template_length" => $request->template_length,
            "template_id" => $request->template_id
        ]);

        return $response->json();
    }
    public function gymnastic(Request $request)
    {
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/gymnastic/";

        $response = Http::post($url, [
            "user_id" => $request->user_id,
            "patient_id" => $request->patient_id,
            "template_length" => $request->template_length,
            "template_id" => $request->template_id
        ]);

        return $response->json();
    }
    public function buildTemplate(Request $request)
    {
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/build_template/";

        $response = Http::post($url, [
            "user_id" => $request->user_id,
        ]);

        return $response->json();
    }
    public function transcribe(Request $request)
    {
       
        $userId = Auth::id(); // Get authenticated user ID
        $url = "https://3fe1-2a02-c207-2242-2625-00-1.ngrok-free.app/transcribe/";

        // Send request to transcribe API
        $response = Http::post($url, [
            "file_name" => $request->file_name,
            "user_id" => $userId, // Ensure correct user ID
            "patient_id" => $request->patient_id,
            "template_length" => $request->template_length,
            "template_id" => $request->template_id
        ]);

        // Check if the API request was successful
        if ($response->successful()) {
            $responseData = $response->json(); // Get the response as an array

            // Save the response data in the transcribe table
            $transcription = Transcribe::create([
                'user_id' => $responseData['user_id'],
                'patient_id' => $responseData['patient_id'],
                'file_name' => $responseData['file_name'],
                'transcribed' => $responseData['transcribed'], // Transcribed text
                'session_id' => $responseData['session_id'],
            ]);

            return response()->json([
                'message' => 'Transcription completed and saved successfully!',
                'data' => $transcription
            ], 201);
        } else {
            return response()->json([
                'message' => 'Transcription API failed!',
                'error' => $response->body()
            ], 500);
        }
    }

    // public function updateLink(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'code' => 'required|string|exists:events,code',
    //         'link' => 'required|url'
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => $validator->errors()->first()
    //         ], 400);
    //     }

    //     $code = $request->input('code');
    //     $link = $request->input('link');

    //     // Update the events table
    //     $updated = DB::table('events')
    //         ->where('code', $code)
    //         ->update(['link' => $link]);

    //     if ($updated) {
    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Link updated successfully'
    //         ], 200);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Failed to update link'
    //     ], 500);
    // }

    public function updateMeetingLink(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'link' => 'required|url'
        ]);

        // Get the event with the given code
        $event = DB::table('events')->where('code', $request->code)->first();

        if (!$event) {
            return response()->json(['success' => false, 'message' => 'Invalid code'], 200);
        }

        // Get the patient email using patient_id
        $patient = DB::table('patients')->where('id', $event->patient_id)->first();

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Patient not found'], 404);
        }

        // Update the event with the link
        DB::table('events')->where('id', $event->id)->update(['link' => $request->link]);

        // Send email to the patient
        Mail::raw("Your Telemedicine video-call link: " . $request->link, function ($message) use ($patient) {
            $message->to($patient->email)
                ->subject("Your Meeting Link");
        });

        return response()->json(['success' => true, 'message' => 'Meeting link updated and email sent']);
    }
}
