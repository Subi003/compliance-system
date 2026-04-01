<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name'];
    
    // public function branches()
    // {
    //     return $this->hasMany(\App\Models\Branch::class);
    // }
}
