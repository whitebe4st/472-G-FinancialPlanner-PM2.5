<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookmarkedTransaction extends Model
{
    protected $table = 'bookmarked_transactions';
    protected $primaryKey = 'bookmark_id';

    protected $fillable = [
        'user_id',
        'description',
        'default_date',
        'amount',
        'type',
        'category'
    ];

    protected $casts = [
        'default_date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 