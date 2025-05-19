<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class feeCategory extends Model
{
    protected $table = 'fee_categories';

    protected $fillable = ['category_name'];

    public function fees()
    {
        return $this->hasMany(Fee::class);
    }


}
