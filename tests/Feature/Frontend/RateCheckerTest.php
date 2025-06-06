<?php

use Illuminate\Support\Facades\Http;

describe('Rate Checker UI', function () {

    test('page loads successfully and form is visible', function () {
        $this->get('/')
            ->assertOk()
            ->assertSee('Get a Quote for Your Next Adventure')
            ->assertSee('Select Unit Type')
            ->assertSee('Select Stay Dates')
            ->assertSee('Number of Guests')
            ->assertSee('Check Rates');
    });

    test('form submission with valid data returns successful rates', function () {
        $mockApiResponse = [
            'Total Charge' => 1250.00,
            'Booking Group ID' => 'BKG-XYZ789',
            'Rooms' => 1,
            'Extras Charge' => 50.00,
            'your_guest_breakdown' => [
                ['age' => 30],
                ['age' => 28],
                ['age' => 7],
            ],
            'Legs' => [
                [
                    'Special Rate Description' => 'Spring Special',
                    'Effective Average Daily Rate' => 416.67,
                    'Total Charge' => 1250.00,
                    'Deposit Breakdown' => [
                        ['Due Date Formatted' => '15/07/2025', 'Due Amount' => 300.00],
                        ['Due Date Formatted' => '01/08/2025', 'Due Amount' => 950.00],
                    ],
                ],
            ],
        ];

        // Ensure you're using `config('app.rates')` here, as per your API controller
        Http::fake([
            config('app.rates') => Http::response($mockApiResponse, 200),
        ]);

        $this->postJson('/api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '01/07/2025',
            'Departure' => '04/07/2025',
            'Occupants' => 3,
            'Ages' => [30, 28, 7],
        ])
        ->assertOk()
        ->assertJson([
            'remote_response' => $mockApiResponse,
        ]);
    });

    test('form submission shows validation errors for missing fields', function () {
        $this->postJson('/api/get-rates', [
            'Unit Name' => '',
            'Arrival' => '01/07/2025',
            'Departure' => '04/07/2025',
            'Occupants' => 1,
            'Ages' => [30],
        ])->assertUnprocessable()
          ->assertJsonValidationErrors('Unit Name');

        $this->postJson('/api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '',
            'Departure' => '04/07/2025',
            'Occupants' => 1,
            'Ages' => [30],
        ])->assertUnprocessable()
          ->assertJsonValidationErrors('Arrival');

        $this->postJson('/api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '01/07/2025',
            'Departure' => '04/07/2025',
            'Occupants' => 0,
            'Ages' => [],
        ])->assertUnprocessable()
          ->assertJsonValidationErrors('Occupants');

        $this->postJson('/api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '01/07/2025',
            'Departure' => '04/07/2025',
            'Occupants' => 2,
            'Ages' => [30, null],
        ])->assertUnprocessable()
          ->assertJsonValidationErrors('Ages.1');
    });

    test('form submission handles external API errors gracefully', function () {
        // This mock response needs to match what your *controller* expects from the *external* API
        // in its error handling.
        Http::fake([
            config('app.rates') => Http::response(['error' => 'Service Unavailable'], 500),
        ]);

        $this->postJson('/api/get-rates', [
            'Unit Name' => 'Kalahari Farmhouse',
            'Arrival' => '01/07/2025',
            'Departure' => '04/07/2025',
            'Occupants' => 1,
            'Ages' => [30],
        ])
        ->assertStatus(500)
        ->assertJson([
            'error' => 'Failed to retrieve rates from remote service. Service Unavailable',
            'status' => 500,
        ]);
    });
});
