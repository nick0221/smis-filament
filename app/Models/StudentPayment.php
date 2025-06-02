<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    public static function getTotalPaidToday(): float
    {
        return static::whereDate('payment_date', now())
            ->where('status', 'paid')
            ->selectRaw('SUM(pay_amount + gcash_pay_amount + bank_pay_amount + other_pay_amount) as totalToday')
            ->value('totalToday') ?? 0;
    }

    public static function getTotalPaidThisMonth(): float
    {
        return static::whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year) // Ensures it's for the current year/month
            ->where('status', 'paid')
            ->selectRaw('SUM(pay_amount + gcash_pay_amount + bank_pay_amount + other_pay_amount) as totalThisMonth')
            ->value('totalThisMonth') ?? 0;
    }

    public static function getTotalPaidThisYear(): float
    {
        return static::whereYear('payment_date', now()->year) // Ensures it's for the current year
            ->where('status', 'paid')
            ->selectRaw('SUM(pay_amount + gcash_pay_amount + bank_pay_amount + other_pay_amount) as totalThisYear')
            ->value('totalThisYear') ?? 0;
    }


    public static function getMonthlyTotalsByPaymentMethod(): array
    {
        $monthlyTotals = array_fill(0, 12, 0);
        $dbDriver = DB::getDriverName();

        if ($dbDriver === 'sqlite') {
            $selectMonth = "strftime('%m', payment_date)";
        } else {
            $selectMonth = "MONTH(payment_date)";
        }

        $payments = static::whereYear('payment_date', now()->year)
            ->where('status', 'paid')
            ->selectRaw("$selectMonth as month, SUM(pay_amount + gcash_pay_amount + bank_pay_amount + other_pay_amount) as total")
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        foreach ($payments as $month => $total) {
            $index = intval($month) - 1;
            $monthlyTotals[$index] = (float) $total;
        }

        return $monthlyTotals;

    }





}
