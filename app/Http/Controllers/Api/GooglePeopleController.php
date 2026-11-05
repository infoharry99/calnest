<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GoogleContact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GooglePeopleController extends Controller
{
    /**
     * Reads contacts that have a birthday.
     * GET /people/me/connections
     */
    public function listConnections(Request $request): JsonResponse
    {
        $pageSize = (int) $request->query('pageSize', 200);
        $pageToken = $request->query('pageToken');

        // CalNest app filters/keeps contacts with birthdays
        $query = GoogleContact::query();

        $offset = 0;
        if ($pageToken && is_numeric(base64_decode($pageToken))) {
            $offset = (int) base64_decode($pageToken);
        }

        $totalPeople = $query->count();
        $contacts = $query->skip($offset)->take($pageSize)->get();

        $nextOffset = $offset + $contacts->count();
        $nextPageToken = ($nextOffset < $totalPeople) ? base64_encode((string)$nextOffset) : null;

        $response = [
            'connections' => $contacts->map->toGoogleArray(),
            'totalPeople' => $totalPeople,
        ];

        if ($nextPageToken) {
            $response['nextPageToken'] = $nextPageToken;
        }

        return response()->json($response, 200);
    }
}
