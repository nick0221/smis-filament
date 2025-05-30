<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class ViewStudentPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Payment information';
    }

    public function getSubheading(): Htmlable|string|null
    {
        return new HtmlString('<span class="font-semibold text-primary">'.$this->getRecord()->reference_number.'</span>');
    }

    public function getRecordTitle(): string|Htmlable
    {
        return 'Payment information for '.$this->getRecord()->enrollment->student->full_name;
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->link()
                ->hiddenLabel()
                ->tooltip('New Payment')
                ->url(PaymentResource::getUrl('create'))
                ->icon('heroicon-o-plus-circle')
                ->label('New Payment'),

            Action::make('print_receipt')
                ->hiddenLabel()
                ->tooltip('Print Receipt')
                ->url(route('payment.print', $this->getRecord()))
                ->openUrlInNewTab()
                ->link()
                ->icon('heroicon-o-printer'),

            DeleteAction::make()
                ->link()
                ->hiddenLabel()
                ->tooltip('Void Payment')
                ->requiresConfirmation()
                ->modalHeading('Void this payment '.$this->getRecord()->reference_number.'?')
                ->icon('heroicon-o-trash')
                ->label('Void')
                ->successNotificationTitle('The payment '.$this->getRecord()->reference_number.' has been voided.')
                ->after(function () {
                    $this->record->status = 'void';
                    $this->record->save();
                }),
        ];
    }


}
