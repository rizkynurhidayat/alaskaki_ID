<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = ['type', 'amount', 'transaction_date', 'description', 'product_id', 'category'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
