<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cloudflare extends Model
{
    protected $table = 'cloudflare';

    protected $fillable = [
        'url',
    ];
}
