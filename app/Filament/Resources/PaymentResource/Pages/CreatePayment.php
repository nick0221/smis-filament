<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
    protected static bool $canCreateAnother = false;

    public function getFormActionsAlignment(): string|Alignment
    {
        return 'right';
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['payment_date'] = Carbon::now();
        $data['status'] = 'paid'; // Set the status to 'paid' // options: pending, paid, failed

        if ($data['payment_method'] == 'cash') {
            $data['pay_amount'] = $data['cash_tendered'] < $data['amount'] ? $data['cash_tendered'] : $data['amount'];

        }

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

}
