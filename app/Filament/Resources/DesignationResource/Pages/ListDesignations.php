<?php

namespace App\Filament\Resources\DesignationResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DesignationResource;

class ListDesignations extends ListRecords
{
    protected static string $resource = DesignationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Create')
                ->form([
                    TextInput::make('title')
                        ->unique(ignoreRecord: true)
                        ->columnSpanFull()
                        ->required(),
                ])
                ->closeModalByClickingAway(false)
                ->modalWidth('md')
                ->modalFooterActionsAlignment('end')
                ->modalCancelAction(false)
                ->modalSubmitActionLabel('Save')
                ->modalHeading('Create New Designation')
                ->successNotification(
                    Notification::make()
                    ->success()
                    ->title('Confirmation')
                    ->body('Designation created successfully.')
                ),


        ];
    }
}
