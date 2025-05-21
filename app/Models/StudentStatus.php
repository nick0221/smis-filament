<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentStatus extends Model
{
    use SoftDeletes;

    protected $table = 'student_statuses';

    protected $fillable = ['key', 'label', 'color', 'description'];

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'status_key', 'key');
    }

    public function trashed()
    {
        return $this->onlyTrashed();
    }

}
