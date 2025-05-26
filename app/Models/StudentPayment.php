<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPayment extends Model
{
    use HasFactory;

    protected $table = 'student_payments';

    protected $fillable = [
        'enrollment_id',
        'amount',
        'payment_method',
        'reference_number',
        'payment_date',
        'notes',
        'status',
        'created_by',
        'deleted_by',
        'updated_by',
        'cash_tendered',
        'change',
        'school_expense_id',
        'pay_amount'


    ];


    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function schoolExpense(): BelongsTo
    {
        return $this->belongsTo(SchoolExpense::class);
    }





    public static function booted()
    {
        static::creating(function ($studentPayment) {
            $studentPayment->reference_number = self::generatePaymentReference();
        });
    }


    public static function generatePaymentReference(): string
    {
        $date = now()->format('mdy'); // e.g., 052324
        $prefix = 'SPR-' . $date . '-';

        return $prefix . str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
    }






}
