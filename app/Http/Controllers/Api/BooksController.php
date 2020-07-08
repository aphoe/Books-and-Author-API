<?php

namespace App\Http\Controllers\Api;

use App\Author;
use App\Book;
use App\Classes\JsonReturnFormat;
use App\Http\Controllers\Controller;
use App\Http\Resources\BookResource;
use App\Http\Resources\BooksCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BooksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //Create validation rules
        $validation = Validator::make($request->all(), [
            'name' => 'nullable|string|min:2',
            'publisher' => 'nullable|string|min:5',
            'country' => 'nullable|string|min:2',
            'release_date' => 'nullable',
        ]);

        //Validate input
        if($validation->fails()){
            $errors = $validation->errors();
            return JsonReturnFormat::validation($errors);
        }

        //Check if release date is a date or int
        if($request->release_date !== null && !is_int($request->release_date) && !strtotime($request->release_date)){
            return JsonReturnFormat::exception(400, 'Release date is not a valid date.');
        }

        //Search
        $books = Book::with('authors')
            ->orderBy('name');

        //Search parameters
        if($request->name !== null){
            $books->where('name', $request->name);
        }

        if($request->publisher !== null){
            $books->where('publisher', $request->publisher);
        }

        if($request->country !== null){
            $books->where('country', $request->country);
        }

        if($request->release_date !== null){
            $books->where(function ($query) use ($request){
                $query->whereYear('release_date', $request->release_date)
                    ->orWhere('release_date', $request->release_date);
            });
        }

        //Get data
        $books = $books->take(100)
            ->get();

        //Return data
        return new BooksCollection($books);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Create validation rules
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'isbn' => 'required|string|max:255',
            'authors' => 'required|string|max:255',
            'publisher' => 'required|string|max:255',
            'country' => 'required|string|max:255',
            'number_of_pages' => 'required|integer|min:1|max:4294967295',
            'release_date' => 'required|date',
        ]);

        //Validate input
        if($validation->fails()){
            $errors = $validation->errors();
            return JsonReturnFormat::validation($errors);
        }

        //Create book model
        $book = new Book();
        $book->name = $request->name;
        $book->isbn = $request->isbn;
        $book->country = $request->country;
        $book->number_of_pages = $request->number_of_pages;
        $book->publisher = $request->publisher;
        $book->release_date = $request->release_date;
        $book->save();

        //Save authors
        $authors = explode('|', $request->authors);
        $authorIds = []; //Array of ids of saved \App\Author models
        foreach($authors as $author){
            $authorModel = Author::firstOrCreate(['name'=>$author]);
            $authorIds[] = $authorModel->id;
        }

        //Attach authors to book
        $book->authors()->attach($authorIds);

        //Return
        return response()->json([
            'status_code' => 201,
            'status' => 'success',
            'data' => [
                'book' => new BookResource($book),
            ]
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $book = Book::findOrFail($id);

        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'data' => new BookResource($book),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $book = Book::findOrFail($id); //Get book
        $name = $book->name; //Saved name of book

        //Create validation rules
        $validation = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'isbn' => 'nullable|string|max:255',
            'authors' => 'nullable|string|max:255',
            'publisher' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'number_of_pages' => 'nullable|integer|min:1|max:4294967295',
            'release_date' => 'nullable|date',
        ]);

        //Validate input
        if($validation->fails()){
            $errors = $validation->errors();
            return JsonReturnFormat::validation($errors);
        }

        //Update book model
        if($request->name !== null) {
            $book->name = $request->name;
        }
        if($request->isbn !== null) {
            $book->isbn = $request->isbn;
        }
        if($request->country !== null) {
            $book->country = $request->country;
        }
        if($request->number_of_pages !== null) {
            $book->number_of_pages = $request->number_of_pages;
        }
        if($request->publisher !== null) {
            $book->publisher = $request->publisher;
        }
        if($request->release_date !== null) {
            $book->release_date = $request->release_date;
        }

        //If any property changed, persist change
        if($book->isDirty()) {
            $book->save();
        }

        //Save authors, if present
        if($request->has('authors')) {
            $authors = explode('|', $request->authors);
            $authorIds = []; //Array of ids of saved \App\Author models
            foreach ($authors as $author) {
                $authorModel = Author::firstOrCreate(['name' => $author]);
                $authorIds[] = $authorModel->id;
            }

            //Attach authors to book
            $book->authors()->sync($authorIds);
        }

        //Return
        return response()->json([
            'status_code' => 200,
            'status' => 'success',
            'message' => 'The book ' . $name . ' was updated successfully.',
            'data' => new BookResource($book),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $book = Book::findOrFail($id); //Get book
        $name = $book->name; //Saved name of book
        $book->delete(); //Delete book

        return response()->json([
            'status_code' => 204,
            'status' => 'success',
            'message' => 'The book ' . $name . ' was deleted successfully.',
            'data' => [],
        ]);
    }
}
