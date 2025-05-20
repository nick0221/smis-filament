<?php

namespace App\Filament\Resources\SchoolExpenseResource\Pages;

use App\Filament\Resources\SchoolExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewSchoolTuition extends ViewRecord
{
    protected static string $resource = SchoolExpenseResource::class;


    public function getRecordTitle(): string|Htmlable
    {
        return $this->getRecord()->expense_name;
    }

    public function getHeading(): string
    {
        return $this->getRecord()->expense_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }

}
