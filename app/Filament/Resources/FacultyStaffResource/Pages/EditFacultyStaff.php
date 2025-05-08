<?php

namespace App\Filament\Resources\FacultyStaffResource\Pages;

use Filament\Actions;
use Illuminate\Support\HtmlString;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\FacultyStaffResource;

class EditFacultyStaff extends EditRecord
{
    protected static string $resource = FacultyStaffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Move to archive')
                ->icon('heroicon-o-archive-box')
                ->modalHeading('Move to archive')
                ->modalIcon('heroicon-o-archive-box')
                ->closeModalByClickingAway(false)
                ->modalDescription(fn ($record) => new HtmlString("Are you sure you want to move to achive <b>{$record->first_name} {$record->last_name}</b>?")),


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
            ->body('Faculty Staff <b>'. strtoupper($this->getRecord()->first_name).' '.strtoupper($this->getRecord()->last_name). '</b> was successfully updated.');
    }









}
