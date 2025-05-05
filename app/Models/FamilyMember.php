<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    /** @use HasFactory<\Database\Factories\FamilyMemberFactory> */
    use HasFactory;

    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'name',
        'relationship',
        'contact',
        'address',
        'occupation',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
