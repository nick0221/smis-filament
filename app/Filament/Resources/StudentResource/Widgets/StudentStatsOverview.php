<?php

namespace App\Filament\Resources\StudentResource\Widgets;

use App\Filament\Resources\StudentResource;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StudentStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $studentCounts = \App\Models\Student::query()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as total_male")
            ->selectRaw("SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as total_female")
            ->first();

        $total = $studentCounts->total ?: 1;
        $malePercentage = round(($studentCounts->total_male / $total) * 100);
        $femalePercentage = round(($studentCounts->total_female / $total) * 100);
        $studentUrl = StudentResource::getUrl();


        return [
            Stat::make('Total Students', $studentCounts->total)
                ->url($studentUrl),

            Stat::make('Male Students', $studentCounts->total_male)
                ->description("{$malePercentage}% of total")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('info')
                ->url(
                    $studentUrl . '?tableFilters[gender][value]=male'
                ),

            Stat::make('Female Students', $studentCounts->total_female)
                ->description("{$femalePercentage}% of total")
                ->descriptionIcon('heroicon-o-user-group')
                ->color('rose')
                ->url(
                    $studentUrl . '?tableFilters[gender][value]=female'
                ),
        ];
    }
}
