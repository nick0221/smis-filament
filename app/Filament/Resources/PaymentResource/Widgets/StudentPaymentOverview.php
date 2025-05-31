<?php

namespace App\Filament\Resources\PaymentResource\Widgets;

use App\Models\StudentPayment;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StudentPaymentOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $currentToday = StudentPayment::getTotalPaidToday();
        $thisMonth = StudentPayment::getTotalPaidThisMonth();
        $thisYear = StudentPayment::getTotalPaidThisYear();




        return [
            Stat::make('Today', number_format($currentToday) ?? 0),
            Stat::make('This Month of '. now()->format('F'), number_format($thisMonth) ?? 0),
            Stat::make('This Year '. now()->format('Y'), number_format($thisYear) ?? 0),

        ];
    }
}
