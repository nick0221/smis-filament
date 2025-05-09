<?php

namespace App\Filament\Resources\GradeLevelResource\Pages;

use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\GradeLevelResource;

class ListGradeLevels extends ListRecords
{
    protected static string $resource = GradeLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->color('success')
                ->label('Create New')
                ->form([
                    TextInput::make('grade_name')
                        ->unique(ignoreRecord: true)
                        ->required(),
                ])
                ->modalHeading('Create New Grade Level')
                ->modalFooterActionsAlignment('end')
                ->closeModalByClickingAway(false)
                ->modalCancelAction(false)
                ->modalWidth('md')
                ->successNotification(
                    Notification::make()
                    ->success()
                    ->title('Confirmation')
                    ->body('New grade level successfully created.')
                )
                ,

        ];
    }
}
