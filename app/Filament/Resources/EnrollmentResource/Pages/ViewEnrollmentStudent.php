<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use App\Filament\Resources\EnrollmentResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Contracts\View\View;

class ViewEnrollmentStudent extends ViewRecord
{
    protected static string $resource = EnrollmentResource::class;

    public function getHeading(): string|Htmlable
    {
        return 'Enrollment - Documents Verification';
    }






}
