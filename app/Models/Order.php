<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;



class Order extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'client_id',
        'worker_id',
        'title',
        'description',
        'price',
        'status',
        'deadline'
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}


