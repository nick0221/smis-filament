<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentPayment extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'pay_amount',
        'gcash_reference_number',
        'bank_reference_number',
        'bank_name',
        'bank_account_number',
        'other_reference_number',
        'other_notes',
        'gcash_pay_amount',
        'bank_pay_amount',
        'other_pay_amount',


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
