<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    /** @use HasFactory<\Database\Factories\SectionFactory> */
    use HasFactory;

    protected $table = 'sections';

    protected $fillable = [
        'section_name',
    ];



    public function teachers()
    {
        return $this->hasMany(FacultyStaff::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }




}
