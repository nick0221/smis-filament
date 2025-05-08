<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\FacultyStaffResource;

class ViewFacultyStaff extends ViewRecord
{
    protected static string $resource = FacultyStaffResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }


    public function getHeading(): string|Htmlable
    {
        return __($this->record->full_name);
    }

    public function getTitle(): string
    {
        return __('SMIS - Student '. $this->record->full_name);
    }


}
