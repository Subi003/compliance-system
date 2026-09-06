<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplianceType extends Model
{
    protected $fillable = [
        'name',
        'terms',
        'attachments',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'attachments' => 'array',
    ];

    public const TERMS_OPTIONS = [
        'half_yearly' => 'Half Yearly',
        'annually'    => 'Annually',
        'five_years'  => 'Five Years',
    ];
}
