<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: modify enum to add 'rejected'
        DB::statement("ALTER TABLE branch_compliance_records MODIFY COLUMN status ENUM('pending','approved','critical','process','renewal','rejected') NOT NULL DEFAULT 'process'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE branch_compliance_records MODIFY COLUMN status ENUM('pending','approved','critical','process','renewal') NOT NULL DEFAULT 'process'");
    }
};
