<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\FacultyStaffResource;

class CreateFacultyStaff extends CreateRecord
{
    protected static string $resource = FacultyStaffResource::class;




    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        return $data;
    }

}
