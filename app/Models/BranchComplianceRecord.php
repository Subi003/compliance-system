<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BranchComplianceRecord extends Model
{
    protected $fillable = [
        'branch_id',
        'compliance_id',
        'from_date',
        'to_date',
        'status',
        'renewal_due',
        'rejection_reason',
    ];

    protected $casts = [
        'from_date'   => 'date',
        'to_date'     => 'date',
        'renewal_due' => 'boolean',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function compliance()
    {
        return $this->belongsTo(Compliance::class);
    }

    /**
     * Computes the logical status from dates + stored status.
     *
     * approved  → sticky (unless to_date expired)
     * rejected  → sticky always (final decision by approver)
     * no dates  → Under Process
     * expired   → Renewal Due
     * ≤15 days  → Critical
     * >15 days  → Approval Pending
     */
    public function getComputedStatusAttribute(): string
    {
        $today = Carbon::today();

        // Rejected is always final — never auto-override
        if ($this->status === 'rejected') {
            return 'rejected';
        }

        // Approved is sticky unless certificate has expired
        if ($this->status === 'approved') {
            if ($this->to_date && $this->to_date->lt($today)) {
                return 'renewal';
            }
            return 'approved';
        }

        // No dates → Under Process
        if (! $this->from_date && ! $this->to_date) {
            return 'process';
        }

        if ($this->to_date) {
            if ($this->to_date->lt($today)) {
                return 'renewal';
            }
            if ($this->to_date->lte($today->copy()->addDays(15))) {
                return 'critical';
            }
        }

        return 'pending';
    }

    public function isExpired(): bool
    {
        return $this->to_date && $this->to_date->lt(Carbon::today());
    }

    /**
     * Whether this record has been given a final decision (approved or rejected).
     * Once final, neither Approve nor Reject should be shown.
     */
    public function isFinalDecision(): bool
    {
        return in_array($this->status, ['approved', 'rejected']);
    }
}
