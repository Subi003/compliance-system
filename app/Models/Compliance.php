<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Compliance extends Model
{
    protected $fillable = ['name', 'compliance_type_id'];

    public function branches()
    {
        return $this->belongsToMany(Branch::class);
    }

    public function complianceType()
    {
        return $this->belongsTo(ComplianceType::class);
    }

    public function records()
    {
        return $this->hasMany(BranchComplianceRecord::class);
    }
}
