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

}
