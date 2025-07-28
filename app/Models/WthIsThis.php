<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WthIsThis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'desc',
        'img',
        'short_desc',
        'subtitle',
    ];
}
