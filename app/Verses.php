<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $version
 * @property int $testament
 * @property int $book
 * @property int $chapter
 * @property int $verse
 * @property string $text
 */
class Verses extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['version', 'testament', 'book', 'chapter', 'verse', 'text'];

    /**
     * Get the user that owns the phone.
     */
    public function books()
    {
        return $this->belongsTo('App\Books', 'book', 'id');
    }

    public static function ref($version, $book, $chapter, $verses)
    {
        $occurs = self::where('version', '=', $version)
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

        return $occurs->get();
    }

}
