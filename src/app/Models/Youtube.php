<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Youtube extends Model
{
    use HasFactory;
    
    protected $table = 'youtubes';
    public $timestamps = true;

    protected $casts = [
    ];

    protected $fillable = [
        'user_id',
        'param_key',
        'title',
		'description',
    ];
}
