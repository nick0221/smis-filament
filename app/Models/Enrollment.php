<?php

namespace App\Models;

use App\Models\StudentStatus;
use Filament\Forms\Components\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use SoftDeletes;

    protected $table = 'enrollments';

    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = [
        'student_id',
        'class_room_id',
        'school_year_from',
        'school_year_to',
        'status_key',
        'initial_average_grade',
        'created_by',
        'section_id',
        'grade_level_id',
        'reference_number',
        'last_updated_by',
        'deleted_by',

    ];

    public function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',

        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function studentStatus(): BelongsTo
    {
        return $this->belongsTo(StudentStatus::class, 'status_key', 'key');
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }


    public function studentDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'student_id', 'student_id');
    }


    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'student_id', 'student_id');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }


    public function scopeStudentExists($query, $studentId, $schoolYearFrom, $schoolYearTo)
    {
        return $query->where([
                ['student_id', '=', $studentId],
                ['school_year_from', '=', $schoolYearFrom],
                ['school_year_to', '=', $schoolYearTo],
            ]);
    }


    public static function booted()
    {
        static::creating(function ($enrollment) {
            $enrollment->reference_number = self::generateReferenceNumber();
        });
    }

    public static function generateReferenceNumber(): string
    {
        return 'ENR-' . date('ymd') . '-' . str_pad(self::count() + 1, 4, '0', STR_PAD_LEFT);
    }

    public function verifyAndSaveDocuments(array $data): void
    {

        try {

            // Save checked requirements as student documents
            $requirements = Requirement::whereIn('id', $data['requirements_presented'])->get();

            foreach ($requirements as $requirement) {
                $this->documents()->firstOrCreate(
                    ['title' => $requirement->document_name],
                    ['description' => $requirement->document_description]
                );
            }

            // Save extra manually added documents
            collect($data['studentDocuments'] ?? [])->each(
                fn ($doc) =>
                $this->documents()->create([
                    'title' => $doc['title'],
                    'description' => $doc['description'] ?? null,
                ])
            );

            // Update enrollment status
            $this->update(['status_key' => 'enrolled']);

        } catch (\Exception $e) {
            Notification::make()
                ->error()
                ->title('Error')
                ->body($e->getMessage())
                ->send();

            return;
        }


    }



    public function uploadDocuments(array $documents): void
    {

        try {

            foreach ($documents as $doc) {
                $this->documents()->create([
                    'title' => $doc['title'],
                    'description' => $doc['description'] ?? null,
                    'file_path' => $doc['file_path'],
                ]);
            }

            Notification::make()
                ->success()
                ->title('Confirmation')
                ->body('Documents has been successfully uploaded.')
                ->send();

        } catch (\Exception $e) {

            Notification::make()
                ->error()
                ->title('Error')
                ->body($e->getMessage())
                ->send();
            return;
        }


    }


}
