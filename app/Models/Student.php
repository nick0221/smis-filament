<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;


    protected $fillable = [
        'first_name',
        'last_name',
        'class_room_id',
        'dob',
        'gender',
        'email',
        'phone',
        'image',
        'address',
        'teacher_id',
        'middle_name',
        'extension_name',
        'user_id',
        'last_updated_by',

    ];
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFullNameAttribute(): string
    {
        return collect([
            ucfirst($this->first_name),
            ucfirst($this->middle_name),
            ucfirst($this->last_name),
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

}
