<?php

namespace App\Filament\Resources\DesignationResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\DesignationResource;

class CreateDesignation extends CreateRecord
{
    protected static string $resource = DesignationResource::class;



    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Confirmation')
            ->body('<b>'.$this->getRecord()->title.'</b> was successfully created.');
    }
}
