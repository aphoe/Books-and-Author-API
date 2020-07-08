<?php

namespace Tests\Feature;

use App\Author;
use App\Book;
use App\Http\Resources\BookResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BookTest extends TestCase
{
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

    public function testBookListedSuccessfully(){
        factory(Book::class)->create([
            'name'=> 'A Game of Thrones',
            'isbn'=> '978-0553103540',
            'number_of_pages'=> 694,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> '1996-08-01'
        ])
        ->each(function($book){
            $book->authors()->save(factory(Author::class)->create(
                [
                    'name' =>'George R. R. Martin',
                ]
                ));
        });

        factory(Book::class)->create([
            'name'=> 'A Clash of Kings',
            'isbn'=> '978-0553108033',
            'number_of_pages'=> 768,
            'publisher'=> 'Bantam Books',
            'country'=> 'United States',
            'release_date'=> '1999-02-02'
        ])
        ->each(function($book){
            $book->authors()->save(factory(Author::class)->create(
                [
                    'name' =>'George R. R. Martin',
                ]
                ));
        });

        $books = factory(Book::class, 2)->create()
        ->each(function($book){
            $book->authors()->save(factory(Author::class)->create());
        })
        ->map(function($book){
            return new BookResource($book);
        });

        $this->json('GET', 'api/v1/books', [], ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'status_code' => 200,
                'status' => 'success',
                'data' => $books->toArray() /*[
                     [
                        'name'=> 'A Game of Thrones',
                        'isbn'=> '978-0553103540',
                        'number_of_pages'=> 694,
                        'publisher'=> 'Bantam Books',
                        'country'=> 'United States',
                        'release_date'=> '1996-08-01',
                        'authors'=> ['George R. R. Martin']
                    ],
                    [
                        'name'=> 'A Clash of Kings',
                        'isbn'=> '978-0553108033',
                        'number_of_pages'=> 768,
                        'publisher'=> 'Bantam Books',
                        'country'=> 'United States',
                        'release_date'=> '1999-02-02',
                        'authors'=> ['George R. R. Martin']
                    ]
                    $books->toArray(),
                ]*/
            ]);
    }
}
