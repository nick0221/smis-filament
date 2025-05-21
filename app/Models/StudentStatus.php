<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentStatus extends Model
{
    protected $table = 'student_statuses';

    protected $fillable = ['key', 'label', 'color', 'description'];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'status_key', 'key');
    }

}
