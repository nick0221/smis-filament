<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected static ?string $title = 'Payment Records';



    public function getHeading(): string|Htmlable
    {
        return 'Payment Records';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


}
