<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // compliance_types: add terms (select) + attachments JSON
        Schema::table('compliance_types', function (Blueprint $table) {
            $table->string('terms')->nullable()->after('name');
            $table->json('attachments')->nullable()->after('terms');
        });

        // branches: add attachments JSON
        Schema::table('branches', function (Blueprint $table) {
            $table->json('attachments')->nullable()->after('first_approver_id');
        });
    }

    public function down(): void
    {
        Schema::table('compliance_types', function (Blueprint $table) {
            $table->dropColumn(['terms', 'attachments']);
        });
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn('attachments');
        });
    }
};
