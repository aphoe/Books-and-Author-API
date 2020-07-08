<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExternalBookTest extends TestCase
{
    /**
     * Test external book listing endpoint
     */
    public function testExternalBookListingSuccessful(){
        $payload = [
            'name' => 'A Game of Thrones',
        ];

        $this->json('GET', 'api/external-books', $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'status' => 'success',
                'data' => [
                    [
                        'name'=> 'A Game of Thrones',
                        'isbn'=> '978-0553103540',
                        'number_of_pages'=> 694,
                        'publisher'=> 'Bantam Books',
                        'country'=> 'United States',
                        'release_date'=> '1996-08-01',
                        'authors'=> [
                            'George R. R. Martin'
                        ]
                    ],
                ]
            ]);
    }

    /**
     * Test external book listing endpoint that returns empty result
     */
    public function testExternalBookListingEmpty(){
        $payload = [
            'name' => Str::random(60),
        ];

        $this->json('GET', 'api/external-books', $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'status' => 'success',
                'data' => [],
            ]);
    }
}
