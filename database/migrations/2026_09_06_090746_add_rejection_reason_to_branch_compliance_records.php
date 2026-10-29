<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branch_compliance_records', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('renewal_due');
        });
    }

    public function down(): void
    {
        Schema::table('branch_compliance_records', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};
