<?php

namespace App\Filament\Resources\SchoolExpenseResource\Pages;

use App\Models\SchoolExpense;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\SchoolExpenseResource;

class EditSchoolExpense extends EditRecord
{
    protected static string $resource = SchoolExpenseResource::class;

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

    public function getRecordTitle(): string|Htmlable
    {
        return $this->getRecord()->expense_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
            $this
                ->getSaveFormAction()
                ->requiresConfirmation()
                ->formId('form')
                ->icon('heroicon-o-check'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): ?string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Confirmation')
            ->body(ucfirst($this->getRecord()->expense_name).' has been successfully updated.');
    }

}
