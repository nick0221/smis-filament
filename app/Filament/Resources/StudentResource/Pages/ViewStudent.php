<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewStudent extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    public function getHeading(): string|Htmlable
    {
        return __($this->record->full_name);
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->record->student_id_number;
    }

    public function getTitle(): string
    {
        return __('SMIS - Student '.$this->record->full_name);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil'),
        ];
    }


}
