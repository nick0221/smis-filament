<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fee extends Model
{
    protected $fillable = [
        'school_expense_id',
        'fee_name',
        'fee_amount',
        'fee_type',
        'fee_category_id',
        'description',
    ];

    public function feeCategory(): BelongsTo
    {
        return $this->belongsTo(FeeCategory::class);
    }

    public function schoolExpense(): BelongsTo
    {
        return $this->belongsTo(SchoolExpense::class);
    }

}
