<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FacultyStaff extends Model
{
    /** @use HasFactory<\Database\Factories\FacultyStaffFactory> */
    use HasFactory;
    use SoftDeletes;


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
        'department_id',
        'photo_path',
        'user_id',
        'last_updated_by',
        'created_by',
        'deleted_by',



    ];

    protected $dates = ['dob'];


    public function designation(): BelongsTo
    {
        return $this->belongsTo(Designation::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }


    public function getFullNameAttribute(): string
    {
        return collect([
            ucfirst($this->last_name).', ',
            ucfirst($this->first_name),
            ucfirst($this->middle_name),
            ucfirst($this->extension_name),
        ])->filter()->join(' ');
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholders/' . ($this->gender === 'female' ? 'placeholder-female.jpg' : 'placeholder-male.jpg'));
    }


    public function getAgeAttribute(): ?int
    {
        return $this->dob ? Carbon::parse($this->dob)->age : null;
    }








}
