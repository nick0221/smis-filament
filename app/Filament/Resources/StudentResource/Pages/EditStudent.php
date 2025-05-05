<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }




    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['last_updated_by'] = auth()->user()->id;
        return $data;
    }



    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->info()
            ->title('Confirmation')
            ->body('Student (<b>'. strtoupper($this->getRecord()->first_name).' '.strtoupper($this->getRecord()->last_name). '</b>) was successfully updated.');
    }

}
