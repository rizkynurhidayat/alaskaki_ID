<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MonthlyProfit extends Model
{
    use HasFactory;

    protected $fillable = ['month', 'year', 'total_income', 'total_expense', 'net_profit'];

    public function distributions()
    {
        return $this->hasMany(ProfitDistribution::class);
    }
}
