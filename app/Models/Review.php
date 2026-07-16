<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory , SoftDeletes;
    protected $fillable = [
        'order_id',
        'client_id',
        'worker_id',
        'rating',
        'comment',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
