<?php

namespace App\Filament\Resources\SchoolExpenseResource\Pages;

use Filament\Actions;
use App\Models\SchoolExpense;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SchoolExpenseResource;

class EditSchoolExpense extends EditRecord
{
    protected static string $resource = SchoolExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function beforeSave(): void
    {
        if (!$this->data['is_active'] && SchoolExpense::where('is_active', true)->count() == 0) {
            Notification::make()
             ->warning()
             ->title('Failed to save')
             ->body('You need to have at least one active tuition fees on the system.')
             ->send();

            $this->halt();
        }

    }

    public function afterSave(): void
    {
        if ($this->data['is_active']) {
            SchoolExpense::where('id', '!=', $this->record->id)->update(['is_active' => false]);
        }
    }



}
