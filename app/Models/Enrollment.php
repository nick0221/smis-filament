<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    protected $table = 'enrollments';
    protected $guarded = [];

    protected $dates = ['created_at', 'updated_at'];
    protected $fillable = ['student_id', 'grade_level_id', 'section_id', 'school_year_from', 'school_year_to', 'status'];


    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function gradeLevel(): BelongsTo
    {
        return $this->belongsTo(GradeLevel::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }




}
