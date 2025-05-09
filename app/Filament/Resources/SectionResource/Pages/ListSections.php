<?php

namespace App\Filament\Resources\SectionResource\Pages;

use Dom\Text;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\SectionResource;

class ListSections extends ListRecords
{
    protected static string $resource = SectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label('Create New')
                ->color('success')
                ->form([
                    TextInput::make('section_name')->required()->label('Section Name')->unique(ignoreRecord: true),
                ])
                ->modalHeading('Create New Section')
                ->modalFooterActionsAlignment('end')
                ->closeModalByClickingAway(false)
                ->modalCancelAction(false)
                ->modalWidth('md')
                ->successNotification(
                    Notification::make()
                    ->success()
                    ->title('Confirmation')
                    ->body('New section successfully created.')
                ),



        ];
    }
}
