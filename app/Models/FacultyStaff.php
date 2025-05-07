<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacultyStaff extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyStaffFactory> */
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'extension_name',
        'email',
        'phone',
        'address',
        'dob',
        'gender',
        'designation',
        'department',
        'photo_path',
    ];

    protected $dates = ['dob'];


    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }


}
