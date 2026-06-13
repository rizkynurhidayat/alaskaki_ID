<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Investor extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'name', 'share_percentage'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
