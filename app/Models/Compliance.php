<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    protected $fillable = ['name'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }
}