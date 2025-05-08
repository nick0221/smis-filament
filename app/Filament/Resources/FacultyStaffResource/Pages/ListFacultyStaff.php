<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use Filament\Actions;
use App\Models\FacultyStaff;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\FacultyStaffResource;

class ListFacultyStaff extends ListRecords
{
    protected static string $resource = FacultyStaffResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Create New'),



        ];
    }
}
