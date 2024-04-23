<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Books;

class BookController extends Controller
{
    public function index()
    {
        $books = Books::all();
        return response()->json($books);
    }

    public function store(Request $request)
    {
        // $book = Books::create($request->all());
        $book = new Books;
        $book->name = $request->name;
        $book->author = $request->author;
        $book->publish_date = $request->publish_date;
        $book->save();
        return response()->json(['message' => 'Book created', 'book' => $book], 201);
    }

    public function show($id)
    {
        $book = Books::find($id);
        if ($book) {
            return response()->json($book);
        }
        return response()->json(['message' => 'Book not found'], 404);
    }

    public function update(Request $request, $id)
    {
        $book = Books::find($id);
        if ($book) {
            $book->name = is_null($request->name) ? $book->name : $request->name;
            $book->author = is_null($request->author) ? $book->author : $request->author;
            $book->publish_date = is_null($request->publish_date) ? $book->publish_date : $request->publish_date;
            $book->save();
            return response()->json(['message' => 'Book updated', 'book' => $book]);
        }
        return response()->json(['message' => 'Book not found'], 404);
    }

    public function destroy($id)
    {
        $book = Books::find($id);
        if ($book) {
            $book->delete();
            return response()->json(['message' => 'Book deleted']);
        }
        return response()->json(['message' => 'Book not found'], 404);
    }
}
