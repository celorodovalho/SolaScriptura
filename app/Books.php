<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $name
 * @property string $abbrev
 * @property string $testament
 */
class Books extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['name', 'abbrev', 'testament'];

    /**
     * Get the comments for the blog post.
     */
    public function verses()
    {
        return $this->hasMany('App\Verses', 'book', 'id');
    }

}
