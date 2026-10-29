<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('branch_compliance_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('compliance_id')->constrained()->cascadeOnDelete();

            $table->date('due_date')->nullable();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();

            $table->enum('status', ['pending', 'approved', 'critical', 'process', 'renewal'])
                ->default('process');

            $table->boolean('renewal_due')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_compliance_records');
    }
};
