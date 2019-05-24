<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ReferencesController extends Controller
{
    public function ref($version, $book, $chapter, $verses)
    {
        $occurs = \App\Verses::ref($version, $book, $chapter, $verses);
        return response()->json($occurs);
    }

    public function books()
    {
        return response()->json(\App\Books::where('abbrev', '=', 'gn')->first()->verses()->where('chapter', '=', 1)->get());
    }
}
