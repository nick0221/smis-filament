<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Teacher extends Model
{
    /** @use HasFactory<\Database\Factories\TeacherFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'dob',
        'gender',
        'qualification',
    ];


    public function classRooms(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function students(): HasMany
{
    return $this->hasMany(Student::class);
}




}
