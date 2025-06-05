<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

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
            'Ages.*' => 'integer|min:0'
        ]);
        /**
         * Children are most welcome at all our venues. Children up to 5 years old are free of charge; for children
         * between the age of 6 and 13 years we charge 50 per cent of the camping rate. For teenagers 13 years and
         * older we charge the adult rates.
         */
        $guests = collect($data['Ages'])->map(function ($age) {
            if ($age <= 5) {
            return ['Age Group' => 'Free'];
            } elseif ($age >= 6 && $age <= 12) {
            return ['Age Group' => 'Half'];
            } else {
            return ['Age Group' => 'Adult'];
            }
        });
        $unitMap = [
            'Luxury Tent' => -2147483637,
            'Standard Room' => -2147483456,
        ];

        $unitName = $data['Unit Name'];

        if (!isset($unitMap[$unitName])) {
            return response()->json([
                'error' => "Unknown Unit Name: {$unitName}"
            ], 422);
        }

        $payload = [
            'Unit Type ID' => $unitMap[$unitName],
            'Arrival' => \Carbon\Carbon::createFromFormat('d/m/Y', $data['Arrival'])->toDateString(),
            'Departure' => \Carbon\Carbon::createFromFormat('d/m/Y', $data['Departure'])->toDateString(),
            'Guests' => $guests->all()
        ];

        $response = Http::post(config('app.rates'), $payload);

        if ($response->failed()) {
            return response()->json([
                'error' => 'Failed to retrieve rates from remote service',
                'status' => $response->status(),
                'message' => $response->body()
            ], $response->status());
        }
        if (!$response->json() || !isset($response->json()['Rates'])) {
            return response()->json([
                'error' => 'Invalid response from remote service',
                'status' => $response->status(),
                'message' => $response->body()
            ], 500);
        }


        return response()->json([
            'unit_name' => $data['Unit Name'],
            'payload_sent' => $payload,
            'remote_response' => $response->json()
        ]);
    }
}
