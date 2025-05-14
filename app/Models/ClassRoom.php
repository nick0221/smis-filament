<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    /** @use HasFactory<\Database\Factories\ClassRoomFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'room_name',
        'room_number',
        'section_id',
        'grade_level_id',
        'average_grade_from',
        'average_grade_to',
        'criteria_description',
        'faculty_staff_id',
        'school_year_from',
        'school_year_to',


    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function adviser(): BelongsTo
    {
        return $this->belongsTo(FacultyStaff::class, 'faculty_staff_id');
    }





}
