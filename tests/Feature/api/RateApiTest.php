<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Testing\Fluent\AssertableJson;

it('returns rates for valid input', function () {
    // Fake the external HTTP response
    Http::fake([
        config('app.rates') => Http::response([
            'Rates' => [
                ['date' => '2025-06-06', 'amount' => 1000],
            ]
        ], 200),
    ]);

    $response = $this->postJson('api/get-rates', [
        'Unit Name' => 'Luxury Tent',
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [4, 15]
    ]);

    $response->assertOk();

    $response->assertJson(fn (AssertableJson $json) =>
        $json->hasAll(['unit_name', 'payload_sent', 'remote_response'])
             ->where('unit_name', 'Luxury Tent')
             ->where('payload_sent.Unit Type ID', -2147483637)
             ->where('payload_sent.Guests.0.Age Group', 'Free')
             ->where('payload_sent.Guests.1.Age Group', 'Adult')
             ->has('remote_response.Rates')
    );
});

it('returns error for unknown unit name', function () {
    $response = $this->postJson('/api/get-rates', [
        'Unit Name' => 'Fake Tent',
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [10, 15]
    ]);

    $response->assertStatus(422)
             ->assertJson([
                 'error' => 'Unknown Unit Name: Fake Tent'
             ]);
});

it('returns validation error for wrong date format', function () {
    $response = $this->postJson('api/get-rates', [
        'Unit Name' => 'Luxury Tent',
        'Arrival' => '2025-06-05',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [10, 14]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['Arrival']);
});

it('returns validation error for empty ages array', function () {
    $response = $this->postJson('api/get-rates', [
        'Unit Name' => 'Luxury Tent',
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => []
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['Ages']);
});


it('returns validation error when required fields are missing', function () {
    $response = $this->postJson('api/get-rates', [
        // 'Unit Name' => missing
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [10, 14]
    ]);

    $response->assertStatus(422)
             ->assertJsonValidationErrors(['Unit Name']);
});

it('handles failed remote API call gracefully', function () {
    Http::fake([
        config('app.rates') => Http::response(null, 500)
    ]);

    $response = $this->postJson('api/get-rates', [
        'Unit Name' => 'Luxury Tent',
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [10, 14]
    ]);

    $response->assertStatus(500)
             ->assertJson([
                 'error' => 'Failed to retrieve rates from remote service'
             ]);
});

it('handles invalid response from remote API', function () {
    Http::fake([
        config('app.rates') => Http::response(['invalid' => 'structure'], 200)
    ]);

    $response = $this->postJson('api/get-rates', [
        'Unit Name' => 'Luxury Tent',
        'Arrival' => '05/06/2025',
        'Departure' => '07/06/2025',
        'Occupants' => 2,
        'Ages' => [10, 14]
    ]);

    $response->assertStatus(500)
             ->assertJson([
                 'error' => 'Invalid response from remote service'
             ]);
});
