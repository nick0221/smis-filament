<?php

namespace App\Models;

use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolExpense extends Model
{
    protected $fillable = [
        'expense_name',
        'description',
        'effectivity_date',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fees(): HasMany
    {
        return $this->hasMany(Fee::class);
    }

    public function feeCategory(): HasMany
    {
        return $this->hasMany(FeeCategory::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function renderActiveTuitionTable(): HtmlString
    {
        $schoolExpense = self::where('is_active', true)->latest()->first();
        $tuitions = $schoolExpense?->fees ?? collect();

        if ($tuitions->isEmpty()) {
            return new HtmlString('<p class="text-sm text-center text-gray-500 text-wrap">No active tuitions available, please contact your administrator.</p>');
        }

        $html = '<table class="w-full text-sm border border-gray-300 rounded-sm shadow-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700">
                        <tr>
                            <th class="px-3 py-2 text-left">Particulars</th>
                            <th class="px-3 py-2 text-right">Amt</th>
                        </tr>
                    </thead>
                    <tbody>';

        foreach ($tuitions as $tuition) {
            $html .= '<tr class="border-t">
                        <td class="px-3 py-2">' . e(ucfirst($tuition->fee_name)) . '</td>
                        <td class="px-3 py-2 text-right">' . number_format($tuition->fee_amount, 2) . '</td>
                    </tr>';
        }

        $html .= '</tbody>';
        $html .= '<tfooter>';
        $html .= '<tr class="border border-double ">';
        $html .= '<td class="px-3 py-2 text-right">Total </td>';
        $html .= '<td class="px-3 py-2 font-semibold text-right"> ₱ ' . number_format($tuitions->sum('fee_amount'), 2) . '</td>';
        $html .= '</tr>';
        $html .= '</tfooter>';
        $html .= '</table>';

        return new HtmlString($html);
    }

}
