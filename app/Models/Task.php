<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable= [
        'title',
        'description',
        'deadline',
        'priority',
        'status',
    ];

    protected $casts = [
        'deadline' => 'datetime',
    ];
}
