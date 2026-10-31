<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id', 'code', 'amount', 'type', 'payment_method', 'status', 'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
