<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            // Drop old string columns if they exist
            if (Schema::hasColumn('branches', 'responsible')) {
                $table->dropColumn('responsible');
            }
            if (Schema::hasColumn('branches', 'first_approver')) {
                $table->dropColumn('first_approver');
            }

            // Add FK columns
            $table->foreignId('responsible_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('first_approver_id')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('responsible_id');
            $table->dropConstrainedForeignId('first_approver_id');
            $table->string('responsible')->nullable();
            $table->string('first_approver')->nullable();
        });
    }
};
