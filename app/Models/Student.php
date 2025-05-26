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
        'student_id_number',

    ];

    public function payments(): HasMany
    {
        return $this->hasMany(StudentPayment::class, 'enrollment_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
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




    public static function booted()
    {
        static::creating(function ($student) {
            $student->student_id_number = self::generateStudentId();
        });
    }

    public static function generateStudentId(): string
    {
        $date = now()->format('mdy'); // e.g., 052324
        $prefix = 'SGID-' . $date . '-';

        // Get the last student's ID (regardless of date)
        $lastStudent = self::orderBy('id', 'desc')->first();

        // Extract the last sequence number if available
        if ($lastStudent && preg_match('/\d{4}$/', $lastStudent->student_id_number, $matches)) {
            $lastSequence = (int) $matches[0];
        } else {
            $lastSequence = 999; // start at 1000
        }

        $nextSequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . $nextSequence;
    }


}
