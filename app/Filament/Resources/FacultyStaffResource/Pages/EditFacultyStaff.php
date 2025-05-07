<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use App\Filament\Resources\FacultyStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFacultyStaff extends EditRecord
{
    protected static string $resource = FacultyStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
