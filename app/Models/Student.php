<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;
    use SoftDeletes;


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
        'middle_name',
        'extension_name',
        'user_id',
        'last_updated_by',

    ];

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
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
            ucfirst($this->last_name).', ',
            ucfirst($this->first_name),
            ucfirst($this->middle_name),
            ucfirst($this->extension_name),
        ])->filter()->join(' ');


    }

    public function getAgeAttribute(): ?int
    {
        return $this->dob ? Carbon::parse($this->dob)->age : null;
    }



    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return asset('images/placeholders/' . ($this->gender === 'female' ? 'placeholder-female.jpg' : 'placeholder-male.jpg'));
    }

}
