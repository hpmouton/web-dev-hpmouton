<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RateController extends Controller
{
    public function getRates(Request $request)
    {
        $data = $request->validate([
            'Unit Name' => 'required|string',
            'Arrival' => 'required|date_format:d/m/Y',
            'Departure' => 'required|date_format:d/m/Y',
            'Occupants' => 'required|integer|min:1',
            'Ages' => 'required|array|min:1',
            'Ages.*' => 'integer|min:0',
        ]);

        $originalAges = $data['Ages'];

        $guestsForRemoteApi = collect($originalAges)->map(function ($age) {
            return ['Age Group' => ($age >= 13) ? 'Adult' : 'Child'];
        })->all();

        $unitMap = [
            'Kalahari Farmhouse' => -2147483637,
            'Klipspringer Camps' => -2147483456,
        ];

        $unitName = $data['Unit Name'];

        if (! isset($unitMap[$unitName])) {
            return response()->json([
                'error' => "Unknown Unit Name: {$unitName}",
            ], 422);
        }

        $payload = [
            'Unit Type ID' => $unitMap[$unitName],
            'Arrival' => Carbon::createFromFormat('d/m/Y', $data['Arrival'])->toDateString(),
            'Departure' => Carbon::createFromFormat('d/m/Y', $data['Departure'])->toDateString(),
            'Guests' => $guestsForRemoteApi,
        ];

        \Log::info('Sending payload to remote API:', $payload);

        $response = Http::post(config('app.rates'), $payload);

        if ($response->failed()) {
            $remoteError = $response->json('error') ?? $response->json('message') ?? $response->body();
            \Log::error('Remote API request failed:', [
                'status' => $response->status(),
                'response_body' => $response->body(),
                'payload_sent' => $payload,
            ]);

            return response()->json([
                'error' => 'Failed to retrieve rates from remote service. '.(is_string($remoteError) ? $remoteError : 'Please try again later.'),
                'status' => $response->status(),
            ], $response->status());
        }

        $jsonResponse = $response->json();

        if (! is_array($jsonResponse) || (! isset($jsonResponse['Rates']) && ! isset($jsonResponse['Legs']))) {
            \Log::error('Invalid or unexpected response from remote API:', [
                'response_body' => $response->body(),
                'payload_sent' => $payload,
            ]);

            return response()->json([
                'error' => 'Invalid response from remote service. The structure was not as expected.',
                'status' => 500,
            ], 500);
        }

        if (isset($jsonResponse['Legs'])) {
            foreach ($jsonResponse['Legs'] as &$leg) {
                if (isset($leg['Deposit Breakdown'])) {
                    foreach ($leg['Deposit Breakdown'] as &$deposit) {
                        if (isset($deposit['Due Day']) && is_numeric($deposit['Due Day'])) {
                            $unixTimestamp = ($deposit['Due Day'] - 25569) * 86400;
                            if ($unixTimestamp > 0) {
                                $deposit['Due Date Formatted'] = Carbon::createFromTimestamp($unixTimestamp)->format('d/m/Y');
                            } else {
                                $deposit['Due Date Formatted'] = 'Invalid Date';
                            }
                        }
                    }
                }
            }
        }

        $processedGuests = [];
        foreach ($originalAges as $age) {
            $category = '';
            if ($age <= 5) {
                $category = 'Free (0-5)';
            } elseif ($age >= 6 && $age <= 12) {
                $category = 'Half Rate (6-12)';
            } else {
                $category = 'Adult (13+)';
            }
            $processedGuests[] = [
                'age' => $age,
                'your_category' => $category,
            ];
        }

        $jsonResponse['your_guest_breakdown'] = $processedGuests;

        return response()->json([
            'unit_name' => $data['Unit Name'],
            'payload_sent' => $payload,
            'remote_response' => $jsonResponse,
        ]);
    }
}
