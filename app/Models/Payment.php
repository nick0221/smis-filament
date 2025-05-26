<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'student_id',
        'amount',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes',
        'payment_status',
        'ending_balance',
        'created_by',
        'deleted_by',
        'updated_by',
        'cash_tendered',
        'change',
    ];



    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }






}
