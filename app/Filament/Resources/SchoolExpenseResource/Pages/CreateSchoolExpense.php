<?php

namespace App\Filament\Resources\SchoolExpenseResource\Pages;

use App\Filament\Resources\SchoolExpenseResource;
use App\Models\SchoolExpense;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class CreateSchoolExpense extends CreateRecord
{
    protected static string $resource = SchoolExpenseResource::class;


    public function getSubheading(): Htmlable|string|null
    {
        return new HtmlString('<small class="text-gray-300"> If you have already added some tuitions for this school, the old ones will be set to inactive. </small>');
    }

    public function beforeCreate(): void
    {

        if ($this->data['is_active']) {
            // Deactivate all currently active records
            $schoolExpense = SchoolExpense::where('is_active', true);
            if ($schoolExpense->count() > 0) {
                $schoolExpense->update(['is_active' => false]);
            }

        }

    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->user()->id;

        return $data;
    }

}
