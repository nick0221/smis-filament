<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum StudentStatus: string implements HasLabel
{
    case Pending = 'pending';
    case Enrolled = 'enrolled';
    case Withdrawn = 'withdrawn';
    case Completed = 'completed';
    case Registered = 'registered';
    case PartiallyPaid = 'partially_paid';
    case FullyPaid = 'fully_paid';
    case Unpaid = 'unpaid';



    public function getLabel(): ?string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Enrolled => 'Enrolled',
            self::Withdrawn => 'Withdrawn',
            self::Completed => 'Completed',
            self::Registered => 'Registered',
            self::PartiallyPaid => 'Partially Paid',
            self::FullyPaid => 'Fully Paid',
            self::Unpaid => 'Unpaid',


        };
    }

    public function getColor(): ?string
    {
        return match ($this) {
            self::Pending => 'danger',
            self::Enrolled => 'primary',
            self::Withdrawn => 'gray',
            self::Completed => 'success',
            self::Registered => 'primary',
            self::PartiallyPaid => 'primary',
            self::FullyPaid => 'primary',
            self::Unpaid => 'warning',

        };
    }

    public function getDescription(): ?string
    {
        return match ($this) {
            self::Pending => 'Student has not been officially enrolled in the system.',
            self::Enrolled => 'Faculty/staff has officially enrolled the student in the system.',
            self::Withdrawn => 'Student has withdrawn from the institution.',
            self::Completed => 'Student has completed the program/course',
            self::Registered => "Student has submitted basic information, but hasn't paid or been enrolled yet.",
            self::PartiallyPaid => 'Student has made a partial tuition payment.',
            self::FullyPaid => 'Student has paid full tuition.',
            self::Unpaid => 'Student has not partially or fully paid tuition.',

        };
    }





}
