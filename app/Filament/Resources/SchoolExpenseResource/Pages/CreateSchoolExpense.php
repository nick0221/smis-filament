<?php

namespace App\Filament\Resources\SchoolExpenseResource\Pages;

use App\Filament\Resources\SchoolExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSchoolExpense extends CreateRecord
{
    protected static string $resource = SchoolExpenseResource::class;



    protected function mutateFormDataBeforeCreate(array $data): array
    {


        $data['created_by'] = auth()->user()->id;

        return $data;
    }



}
