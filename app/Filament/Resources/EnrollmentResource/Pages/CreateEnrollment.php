<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Filament\Actions;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EnrollmentResource;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;




    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $initialGrade = $data['initial_average_grade'] ?? 0;

        if ($this->studentAlreadyEnrolled($data)) {
            return $this->failWithWarning('The student is already enrolled for the selected school year and section.');
        }

        // Early checks
        if (!$this->gradeLevelExists($data['grade_level_id'])) {
            return $this->failWithWarning('The chosen grade level has not been added yet');
        }

        if ($this->shouldCheckSection($data) && !$this->sectionExistsForGrade($data)) {
            return $this->failWithWarning('The chosen section for the grade level has not been added yet');
        }



        // Find classroom based on initial grade
        $classroom = $this->findClassroomForGrade($initialGrade, $data['grade_level_id']);

        if (!$classroom && $initialGrade == 0) {
            return $this->failWithWarning('No classroom found for the initial average grade.');
        }

        $data['class_room_id'] = $classroom->id;
        $data['section_id'] = $classroom->section_id;

        return $data;
    }




    // Optional helper method for sending consistent warnings
    protected function sendWarningNotification(string $message): void
    {
        Notification::make()
            ->warning()
            ->title('Confirmation')
            ->body($message)
            ->duration(5000)
            ->icon('heroicon-o-exclamation-triangle')
            ->actions([
                Action::make('add_classroom')
                    ->button()
                    ->url(route('filament.app.resources.class-rooms.create'))
                    ->color('success')
                    ->openUrlInNewTab()
                    ->label('Add Classroom'),

            ])
            ->send();
    }



    protected function failWithWarning(string $message): array
    {
        $this->sendWarningNotification($message);
        $this->halt();
        return []; // for type consistency
    }

    protected function gradeLevelExists(int $gradeLevelId): bool
    {
        return ClassRoom::where('grade_level_id', $gradeLevelId)->exists();
    }

    protected function sectionExistsForGrade(array $data): bool
    {
        return ClassRoom::where('grade_level_id', $data['grade_level_id'])
            ->where('section_id', $data['section_id'])
            ->exists();
    }

    protected function shouldCheckSection(array $data): bool
    {
        return ($data['initial_average_grade'] ?? 0) == 0;
    }

    protected function studentAlreadyEnrolled(array $data): bool
    {
        return Enrollment::studentExists(
            $data['student_id'],
            $data['school_year_from'],
            $data['school_year_to']
        )->exists();
    }

    protected function findClassroomForGrade(float|int $grade, int $gradeLevelId): ?ClassRoom
    {
        return ClassRoom::where('average_grade_from', '<=', $grade)
            ->where('average_grade_to', '>=', $grade)
            ->where('grade_level_id', $gradeLevelId)
            ->first();
    }



}
