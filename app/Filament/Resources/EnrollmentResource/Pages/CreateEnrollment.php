<?php

namespace App\Filament\Resources\EnrollmentResource\Pages;

use Filament\Actions;
use App\Models\ClassRoom;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\EnrollmentResource;

class CreateEnrollment extends CreateRecord
{
    protected static string $resource = EnrollmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['status_key'] = 'pending';
        $initialGrade = $data['initial_average_grade'] ?? null;


        // Check if already enrolled
        if ($this->studentAlreadyEnrolled($data)) {
            return $this->failWithWarning('The student already exists for the specified school year and section.', true);
        }

        // Grade level must exist
        if (!$this->gradeLevelExists($data['grade_level_id'])) {
            return $this->failWithWarning('The chosen grade level has not been added to classroom records.');
        }

        // If no grade, we rely on selected section – it must be valid
        if (is_null($initialGrade) && !$this->sectionExistsForGrade($data)) {
            return $this->failWithWarning('The chosen section for the grade level has not been added to classroom records.');
        }

        // Resolve classroom
        $classroom = $initialGrade !== null
            ? $this->findClassroomForGrade($initialGrade, $data['grade_level_id'])
            : $this->findClassroomBySection($data['grade_level_id'], $data['section_id']);

        if (!$classroom) {
            return $this->failWithWarning('No classroom found for the provided data.');
        }

        $data['class_room_id'] = $classroom->id;
        $data['section_id'] = $classroom->section_id;

        return $data;
    }

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
        return [];
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

    protected function studentAlreadyEnrolled(array $data): bool
    {
        return Enrollment::where('student_id', $data['student_id'])
            ->where('school_year_from', $data['school_year_from'])
            ->where('school_year_to', $data['school_year_to'])
            ->exists();
    }

    protected function findClassroomForGrade(float|int $grade, int $gradeLevelId): ?ClassRoom
    {
        return ClassRoom::where('average_grade_from', '<=', $grade)
            ->where('average_grade_to', '>=', $grade)
            ->where('grade_level_id', $gradeLevelId)
            ->first();
    }

    protected function findClassroomBySection(int $gradeLevelId, int $sectionId): ?ClassRoom
    {
        return ClassRoom::where('grade_level_id', $gradeLevelId)
            ->where('section_id', $sectionId)
            ->first();
    }



    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view-enrollment', ['record' => $this->getRecord()]);
    }


}
