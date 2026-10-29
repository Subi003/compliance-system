<?php

namespace App\Observers;

use App\Models\BranchComplianceRecord;
use Carbon\Carbon;

class BranchComplianceRecordObserver
{
    /**
     * Runs before every create or update.
     * Automatically computes status and renewal_due from dates.
     *
     * Rules:
     *   - No from_date AND no to_date  → Under Process (process)
     *   - to_date in the past          → Renewal Due   (renewal)
     *   - to_date within next 15 days  → Critical      (critical)
     *   - to_date > 15 days away       → Approval Pending (pending)
     *
     * Exception: if the status is already 'approved' it was explicitly
     * approved by the approver — preserve it UNLESS the to_date has
     * now expired, in which case flip to renewal.
     */
    public function saving(BranchComplianceRecord $record): void
    {
        $from  = $record->from_date;
        $to    = $record->to_date;
        $today = Carbon::today();

        // Rejected is always final — observer never overrides it
        if ($record->status === 'rejected') {
            return;
        }

        // If currently approved, only override if to_date is now expired
        if ($record->status === 'approved') {
            if ($to && $to->lt($today)) {
                $record->status      = 'renewal';
                $record->renewal_due = true;
            }
            // Otherwise keep approved as-is
            return;
        }

        // No dates at all → Under Process
        if (! $from && ! $to) {
            $record->status      = 'process';
            $record->renewal_due = false;
            return;
        }

        if ($to) {
            // to_date already past → Renewal Due
            if ($to->lt($today)) {
                $record->status      = 'renewal';
                $record->renewal_due = true;
                return;
            }

            // to_date within 15 days → Critical
            if ($to->lte($today->copy()->addDays(15))) {
                $record->status      = 'critical';
                $record->renewal_due = false;
                return;
            }
        }

        // to_date exists and is > 15 days away → Approval Pending
        $record->status      = 'pending';
        $record->renewal_due = false;
    }
}
