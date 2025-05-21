<?php

namespace App\Filament\Resources\StudentStatusResource\Pages;

use App\Filament\Resources\StudentStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStudentStatuses extends ListRecords
{
    protected static string $resource = StudentStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
