<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Designation extends Model
{
    /** @use HasFactory<\Database\Factories\DesignationFactory> */
    use HasFactory;

    protected $table = 'designations';

    protected $fillable = ['title'];





}
