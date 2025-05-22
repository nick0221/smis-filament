<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Requirement extends Model
{
    protected $table = 'requirements';

    protected $fillable = [
        'document_name',
        'document_description',
    ];



    public function enrollments(): BelongsToMany
    {
        return $this->belongsToMany(Enrollment::class);
    }


}
