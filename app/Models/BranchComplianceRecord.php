<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchComplianceRecord extends Model
{
    protected $fillable = [
    'branch_id',
    'compliance_id',
    'due_date',
    'status',
    'renewal_due',
    ];
}
