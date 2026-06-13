<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitDistribution extends Model
{
    use HasFactory;

    protected $fillable = ['monthly_profit_id', 'investor_id', 'calculated_amount', 'status'];

    public function monthlyProfit()
    {
        return $this->belongsTo(MonthlyProfit::class);
    }

    public function investor()
    {
        return $this->belongsTo(Investor::class);
    }
}
