<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Program extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'time',
        'date',
        'group_id',

    ];

    public function group()
    {
        return $this->belongsTo(Group::class, 'id');
    }
}
