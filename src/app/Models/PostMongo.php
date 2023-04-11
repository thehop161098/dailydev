<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PostMongo extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'posts';

    public $timestamps = true;

    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'post_id',
        'title',
        'image',
		'readTime',
		'permalink',
		'created_at',
		'updated_at',
    ];
}
