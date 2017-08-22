<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class ReferencesController extends Controller
{
    public function ref($version, $book, $chapter, $verses)
    {
        $occurs = \App\Verses::where('version', '=', $version)
            ->where('book', '=', $book)
            ->where('chapter', '=', $chapter);
        $verses = explode(',', $verses);

        $verseIn = collect([]);

        foreach ($verses as $verse) {
            if (strpos($verse, '-')) {
                $between = explode('-', $verse);
                sort($between);
                for ($i = $between[0]; $i <= $between[1]; $i++) {
                    $verseIn->push($i);
                }
            } else
                $verseIn->push($verse);
        }

        if ($verseIn->isNotEmpty())
            $occurs->whereIn('verse', $verseIn->sort()->toArray());

        return response()->json($occurs->get());
    }

    public function books()
    {
        return response()->json(\App\Books::where('abbrev', '=', 'gn')->first()->verses()->where('chapter', '=', 1)->get());
    }
}