<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;

describe('Rate Checker API', function () {

    $validPayload = [
        'Unit Name' => 'Kalahari Farmhouse',
        'Arrival' => '13/06/2025',
        'Departure' => '21/06/2025',
        'Occupants' => 1,
        'Ages' => [25],
    ];

    $mockSuccessfulRemoteResponse = [
        'Location ID' => -2147473614,
        'Total Charge' => 260000,
        'Extras Charge' => 0,
        'Booking Group ID' => 'Rate Check',
        'Legs' => [
            [
                'Special Rate ID' => 1744830483,
                'Effective Average Daily Rate' => 32500,
                'Total Charge' => 260000,
                'Adult Count' => 1,
                'Booking Client ID' => -2146822694,
                'Category' => 'STANDARD',
                'Child Ages' => [],
                'Deposit Breakdown' => [
                    [
                        'Due Day' => 45817,
                        'Due Amount' => 260000,
                    ],
                ],
                'Deposit Rule ID' => -2147483643,
                'Error Code' => 0,
                'Extras' => [],
                'Guests' => [
                    ['Age Group' => 'Adult', 'Age' => 25],
                ],
                'Special Rate Code' => 'JNKFH20CAP',
                'Special Rate Description' => '* STANDARD RATE CAMPING - Kalahari Farmhouse ',
                'Special Rate Requested ID' => 1744830483,
                'Total Charge' => 260000,
            ],
        ],
        'Rooms' => 8,

    ];

    it('returns rates for valid input and processes remote API response correctly', function () use ($validPayload, $mockSuccessfulRemoteResponse) {
        Http::fake([
            config('app.rates') => Http::response($mockSuccessfulRemoteResponse, 200),
        ]);

        $response = $this->postJson('api/get-rates', $validPayload);

        $response->assertOk();

        $response->assertJson(
            fn(AssertableJson $json) => $json->hasAll(['unit_name', 'payload_sent', 'remote_response'])
                ->where('unit_name', $validPayload['Unit Name'])
                ->where('payload_sent.Unit Type ID', -2147483637)
                ->where('payload_sent.Arrival', '2025-06-13')
                ->where('payload_sent.Departure', '2025-06-21')
                ->where('payload_sent.Guests.0.Age Group', 'Adult')
                ->has(
                    'remote_response',
                    fn(AssertableJson $json) => $json->where('Total Charge', 260000)
                        ->has('Legs', 1)
                        ->has(
                            'Legs.0',
                            fn(AssertableJson $json) => $json->where('Special Rate Description', '* STANDARD RATE CAMPING - Kalahari Farmhouse ')
                                ->where('Total Charge', 260000)
                                ->where('Adult Count', 1)
                                ->where('Child Ages', [])
                                ->has('Deposit Breakdown', 1)
                                ->has(
                                    'Deposit Breakdown.0',
                                    fn(AssertableJson $json) => $json->where('Due Amount', 260000)
                                        ->where('Due Date Formatted', '09/06/2025')
                                        ->etc()
                                )
                                ->etc()
                        )
                        ->has('your_guest_breakdown', 1)
                        ->has(
                            'your_guest_breakdown.0',
                            fn(AssertableJson $json) => $json->where('age', 25)
                                ->where('your_category', 'Adult (13+)')
                        )
                        ->etc()
                )
                ->etc()
        );
    });

    it('returns rates for Klipspringer Camps with specific unit ID', function () use ($mockSuccessfulRemoteResponse) {
        $campingPayload = [
            'Unit Name' => 'Klipspringer Camps',
            'Arrival' => '10/07/2025',
            'Departure' => '15/07/2025',
            'Occupants' => 2,
            'Ages' => [5, 18],
        ];

        $campingMockResponse = $mockSuccessfulRemoteResponse;
        $campingMockResponse['Legs'][0]['Special Rate Description'] = 'Camping Rate';
        $campingMockResponse['Legs'][0]['Total Charge'] = 50000;
        $campingMockResponse['Legs'][0]['Effective Average Daily Rate'] = 10000;
        $campingMockResponse['Legs'][0]['Adult Count'] = 1;
        $campingMockResponse['Legs'][0]['Child Ages'] = [5];
        $campingMockResponse['Total Charge'] = 50000;
        $campingMockResponse['your_guest_breakdown'] = [
            ['age' => 5, 'your_category' => 'Free (0-5)'],
            ['age' => 18, 'your_category' => 'Adult (13+)'],
        ];

        Http::fake([
            config('app.rates') => Http::response($campingMockResponse, 200),
        ]);

        $response = $this->postJson('api/get-rates', $campingPayload);

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $json) => $json->where('unit_name', $campingPayload['Unit Name'])
                ->where('payload_sent.Unit Type ID', -2147483456)
                ->where('payload_sent.Guests.0.Age Group', 'Child')
                ->where('payload_sent.Guests.1.Age Group', 'Adult')
                ->where('remote_response.Total Charge', 50000)
                ->has(
                    'remote_response.Legs.0',
                    fn(AssertableJson $json) => $json->where('Special Rate Description', 'Camping Rate')
                        ->where('Adult Count', 1)
                        ->where('Child Ages', [5])
                        ->etc()
                )
                ->has('remote_response.your_guest_breakdown', 2)
                ->has(
                    'remote_response.your_guest_breakdown.0',
                    fn(AssertableJson $json) => $json->where('age', 5)
                        ->where('your_category', 'Free (0-5)')
                )
                ->has(
                    'remote_response.your_guest_breakdown.1',
                    fn(AssertableJson $json) => $json->where('age', 18)
                        ->where('your_category', 'Adult (13+)')
                )
                ->etc()
        );
    });

    it('returns error for unknown unit name', function () {
        $response = $this->postJson('/api/get-rates', [
            'Unit Name' => 'NonExistentUnit',
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 15],
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Unknown Unit Name: NonExistentUnit',
            ]);
    });

    it('returns validation error for wrong date format', function () {
        $response = $this->postJson('api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '2025-06-05',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 14],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Arrival']);
    });

    it('returns validation error for empty ages array', function () {
        $response = $this->postJson('api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Ages']);
    });

    it('returns validation error when required fields are missing', function () {
        $response = $this->postJson('api/get-rates', [
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 14],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['Unit Name']);
    });

    it('handles failed remote API call gracefully', function () {
        Http::fake([
            config('app.rates') => Http::response('Remote server error', 500),
        ]);

        $response = $this->postJson('api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 14],
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to retrieve rates from remote service. Remote server error',
            ]);
    });

    it('handles invalid response from remote API (missing Legs)', function () {
        Http::fake([
            config('app.rates') => Http::response([
                'Location ID' => 123,
                'Total Charge' => 1000,
            ], 200),
        ]);

        $response = $this->postJson('api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 14],
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Invalid response from remote service. The structure was not as expected.',
            ]);
    });

    it('handles invalid response from remote API (non-array response)', function () {
        Http::fake([
            config('app.rates') => Http::response('Not a JSON array', 200),
        ]);

        $response = $this->postJson('api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '05/06/2025',
            'Departure' => '07/06/2025',
            'Occupants' => 2,
            'Ages' => [10, 14],
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Invalid response from remote service. The structure was not as expected.',
            ]);
    });

    it('correctly processes deposit breakdown date format', function () use ($validPayload, $mockSuccessfulRemoteResponse) {
        $mockWithDeposit = $mockSuccessfulRemoteResponse;
        $mockWithDeposit['Legs'][0]['Deposit Breakdown'][0]['Due Day'] = 45817;

        Http::fake([
            config('app.rates') => Http::response($mockWithDeposit, 200),
        ]);

        $response = $this->postJson('api/get-rates', $validPayload);

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $json) => $json->has(
                'remote_response.Legs.0.Deposit Breakdown.0',
                fn(AssertableJson $json) => $json->where('Due Date Formatted', '09/06/2025')
                    ->etc()
            )
                ->etc()
        );
    });

    it('correctly categorizes guests in your_guest_breakdown', function () use ($mockSuccessfulRemoteResponse) {
        $payloadWithMixedAges = [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '13/06/2025',
            'Departure' => '21/06/2025',
            'Occupants' => 3,
            'Ages' => [3, 8, 20],
        ];

        Http::fake([
            config('app.rates') => Http::response($mockSuccessfulRemoteResponse, 200),
        ]);

        $response = $this->postJson('api/get-rates', $payloadWithMixedAges);

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $json) => $json->has('remote_response.your_guest_breakdown', 3)
                ->has(
                    'remote_response.your_guest_breakdown.0',
                    fn(AssertableJson $json) => $json->where('age', 3)->where('your_category', 'Free (0-5)')
                )
                ->has(
                    'remote_response.your_guest_breakdown.1',
                    fn(AssertableJson $json) => $json->where('age', 8)->where('your_category', 'Half Rate (6-12)')
                )
                ->has(
                    'remote_response.your_guest_breakdown.2',
                    fn(AssertableJson $json) => $json->where('age', 20)->where('your_category', 'Adult (13+)')
                )
                ->etc()
        );
    });
});
