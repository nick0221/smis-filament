<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\EnrollmentResource;
use Filament\Forms\Components\Repeater;

class ViewEnrollmentStudent extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    public function getSubheading(): Htmlable|string|null
    {
        return new HtmlString('<span class="font-semibold text-primary">'.$this->getRecord()->reference_number.'</span>');
    }

    /**s
     * @return string|Htmlable
     */
    public function getHeading(): string|Htmlable
    {
        return 'Document Verification';
    }

    public function getRecordTitle(): string|Htmlable
    {
        return 'Reference No: '.$this->getRecord()->reference_number;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('confirm_verification')
                ->label('Confirm Verification')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->modalWidth('sm')
                ->closeModalByClickingAway(false)
                ->slideOver()
                ->modalFooterActionsAlignment('center')
                ->form([
                    Repeater::make('studentDocuments')
                        ->addActionLabel('Add more')
                        ->columnSpanFull()
                        ->simple(
                            TextInput::make('title')

                                ->required(),
                        ),
                ])
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Verification confirmed!')
                ),

            DeleteAction::make('delete')
                ->modalIcon('heroicon-o-archive-box-x-mark')
                ->modalHeading('Void this enrollment?')
                ->icon('heroicon-o-archive-box-x-mark')
                ->label('Void')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title('Transaction voided!')
                )
                ->before(function () {
                    $this->record->deleted_by = Auth::user()->id;
                    $this->record->save();

                })
        ];
    }

}
