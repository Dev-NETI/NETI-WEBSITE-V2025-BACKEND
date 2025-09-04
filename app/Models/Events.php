<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Events extends Model
{
    protected $table = 'events';
    protected $fillable = [
        'category',
        'content',
        'description',
        'featured',
        'location',
        'startDate',
        'endDate',
        'status',
        'title',
    ];
}
