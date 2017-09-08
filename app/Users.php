<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $telegram_id
 * @property boolean $is_bot
 * @property string $first_name
 * @property string $username
 * @property string $language_code
 * @property string $version
 */
class Users extends Model
{
    use SoftDeletes;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * @var array
     */
    protected $fillable = ['telegram_id', 'is_bot', 'first_name', 'last_name', 'username', 'language_code', 'version'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}
