<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = [
        'title',
        'location',
        'company_id',
        'responsible_id',
        'first_approver_id',
        'attachments',
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function compliances()
    {
        return $this->belongsToMany(\App\Models\Compliance::class);
    }

    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class);
    }

    public function responsible()
    {
        return $this->belongsTo(\App\Models\User::class, 'responsible_id');
    }

    public function firstApprover()
    {
        return $this->belongsTo(\App\Models\User::class, 'first_approver_id');
    }

    public function complianceRecords()
    {
        return $this->hasMany(\App\Models\BranchComplianceRecord::class);
    }
}
