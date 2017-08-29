<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $telegram_id
 * @property boolean $is_bot
 * @property string $first_name
 * @property string $username
 * @property string $language_code
 */
class Users extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['telegram_id', 'is_bot', 'first_name', 'username', 'language_code'];

}
