<?php

namespace App\Http\Controllers\Api;

use App\Author;
use App\Book;
use App\Classes\JsonReturnFormat;
use App\Http\Controllers\Controller;
use App\Http\Resources\ExternalBooksCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExternalBooksController extends Controller
{
    private $iafApi;
    private $client;

    public function __construct()
    {
        $this->iafApi = 'https://www.anapioficeandfire.com/api/';
        $this->client = new Client([
            'base_uri' => $this->iafApi,
        ]);
    }

    public function index(Request $request){
        //Create validation rules
        $validation = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        //Validate input
        if($validation->fails()){
            $errors = $validation->errors();
            return JsonReturnFormat::validation($errors);
        }


        //Query API
        try {
            $response = $this->client->request('GET', 'books', [
                'query' => ['name' => $request->name]
            ]);
            $results = json_decode($response->getBody(), false);

            $books = [];
            foreach ($results as $result){
                $book = new Book();
                $book->name = $result->name;
                $book->isbn = $result->isbn;
                $book->country = $result->country;
                $book->number_of_pages = $result->numberOfPages;
                $book->publisher = $result->publisher;
                $book->release_date = $result->released;

                $book->authors = $result->authors;

                $books[] = $book;
            }

            return new ExternalBooksCollection($books);

        }catch (ConnectException $exception){
            return JsonReturnFormat::exception(404, 'Could not resolve host: ' . $this->iafApi);
        }catch (\Exception $exception){
            return JsonReturnFormat::exception(400, $exception->getMessage());
        }
    }
}
