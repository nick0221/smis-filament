<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\DepartmentResource;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Create')
                ->form([
                    TextInput::make('name')
                        ->columnSpanFull()
                        ->unique(ignoreRecord: true)
                        ->required(),

                    TextInput::make('code')
                        ->columnSpanFull(),
                ])
                ->modalHeading('Create New Department')
                ->modalFooterActionsAlignment('end')
                ->closeModalByClickingAway(false)
                ->modalCancelAction(false)
                ->modalWidth('md')
                ->successNotification(
                    Notification::make()
                    ->success()
                    ->title('Confirmation')
                    ->body('New department successfully created.')
                ),
        ];
    }
}
