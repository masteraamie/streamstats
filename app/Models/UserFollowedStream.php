<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollowedStream extends Model
{
    use HasFactory;

    protected $table = 'user_followed_streams';
    protected $guarded = [];
}
