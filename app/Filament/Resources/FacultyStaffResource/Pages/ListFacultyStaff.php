<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use App\Filament\Resources\FacultyStaffResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\MaxWidth;

class ListFacultyStaff extends ListRecords
{
    protected static string $resource = FacultyStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),



        ];
    }
}
