<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Requirement extends Model
{
    use HasFactory;

    protected $table = 'requirements';

    protected $fillable = [
        'document_name',
        'document_description',
    ];



    public function enrollments(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }


}
