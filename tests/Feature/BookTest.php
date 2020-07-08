<?php

namespace Tests\Feature;

use App\Author;
use App\Book;
use App\Http\Resources\BookResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookTest extends TestCase
{
    /**
     * Test book creation end point
     *
     * @return void
     */
    public function testBookCreatedSuccessully(){
        $bookData = [
            'name'=> 'A Game of Thrones',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 694,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> '1996-08-01',
            'authors'=> 'George R. R. Martin'
        ];

        $this->json('POST', 'api/v1/books', $bookData, ['Accept' => 'application/json'])
            ->assertStatus(201)
            ->assertJson([
                'status_code' => 201,
                'status' => 'success',
                "data" => [
                    'book' => [
                        'name'=> 'A Game of Thrones',
                        'isbn'=> '978-0553103540',
                        'number_of_pages'=> 694,
                        'publisher'=> 'Bantam Books',
                        'country'=> 'United States',
                        'release_date'=> '1996-08-01',
                        'authors'=> ['George R. R. Martin']
                    ],
                ],
            ]);
    }

    /**
     * Test book read end point
     *
     * @return void
     */
    public function testBookReadSuccessfully(){
        $now = now()->format('Y-m-d');
        factory(Book::class)->create([
            'name'=> 'A Game of Thrones',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 694,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> $now
        ])
        ->each(function($book){
            $book->authors()->save(factory(Author::class)->create(
                [
                    'name' =>'George R. R. Martin',
                ]
                ));
        });

        $payload = [
            'name' => 'A Game of Thrones',
            'release_date' => $now,
        ];

        $this->json('GET', 'api/v1/books', $payload, ['Accept' => 'application/json'])
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
                        'release_date'=> $now,
                        'authors'=> ['George R. R. Martin']
                    ],
                ]
            ]);
    }

    /**
     * Test book read endpoint that returns empty result
     *
     * @return void
     */
    public function testBooksReadEmpty(){
        $payload = [
            'name' => Str::random(300),
            'release_date' => now()->addYears(50)->format('Y-m-d'),
        ];

        $this->json('GET', 'api/v1/books', $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'status' => 'success',
                'data' => []
            ]);
    }

    /**
     * Test book update endpoint
     *
     * @return void
     */
    public function testBooksUpdatedSuccessfully(){
        $now = now()->format('Y-m-d');
        $book  = factory(Book::class)->create([
            'name'=> 'A Game of Thrones',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 694,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> $now
        ]);

        $payload = [
            'name'=> 'Ice and Fire',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 804,
            'publisher'=> 'Bantam Books',
            'country'=> 'England',
            'release_date'=> $now,
            'authors' => 'George R. R. Martin|Jon Snow'
        ];

        $this->json('PATCH', 'api/v1/books/' . $book->id, $payload, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'status' => 'success',
                'message' => 'The book A Game of Thrones was updated successfully.',
                'data' => [
                    'name'=> 'Ice and Fire',
                    'isbn'=> '978-0553103540',
                    'number_of_pages'=> 804,
                    'publisher'=> 'Bantam Books',
                    'country'=> 'England',
                    'release_date'=> $now,
                    'authors' => ['George R. R. Martin', 'Jon Snow']
                ]
            ]);
    }

    /**
     * Test book delete endpoint
     *
     * @return void
     */
    public function testBookDeleted(){
        $now = now()->format('Y-m-d');
        $book  = factory(Book::class)->create([
            'name'=> 'A Game of Thrones',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 694,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> $now
        ]);

            $this->json('DELETE', 'api/v1/books/' . $book->id, [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 204,
                'status' => 'success',
                'message' => 'The book A Game of Thrones was deleted successfully.',
                'data' => []
            ]);
    }
}
