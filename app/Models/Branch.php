<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'title',
        'location',
        'company_id',
        'responsible',
        'first_approver',
    ];

    // 🔥 YE ADD KARNA HAI
    public function compliances()
    {
        return $this->belongsToMany(\App\Models\Compliance::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }
}