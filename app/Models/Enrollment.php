<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'status',
        'initial_average_grade',
        'created_by',
        'section_id',
        'grade_level_id',

    ];


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

    public function scopeStudentExists($query, $studentId, $schoolYearFrom, $schoolYearTo)
    {
        return $query->where([
                ['student_id', '=', $studentId],
                ['school_year_from', '=', $schoolYearFrom],
                ['school_year_to', '=', $schoolYearTo],
            ]);
    }


}
