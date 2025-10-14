<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    use HasFactory;

    protected $table = 'songs';
    protected $fillable = [
        'name',
        'thumbnail',
        'price',
        'youtube_link',
        'beatmap_easy',
        'beatmap_normal',
        'beatmap_hard',
    ];
}
