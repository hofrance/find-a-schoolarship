<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Detection extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_name','title','country','level','language','score','deadline','amount','item_url','first_seen','last_seen',
        'source_id','provider','category','funding_type','region','fields','tags','source_url','summary'
    ];

    protected $casts = [
        'score' => 'integer',
        'deadline' => 'date',
        'first_seen' => 'datetime',
        'last_seen' => 'datetime',
    ];
}
