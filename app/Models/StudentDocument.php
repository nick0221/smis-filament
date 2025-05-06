<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDocument extends Model
{

    protected $table = 'student_documents';

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'file_path',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Student::class);
    }

}
