<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_page_permissions', function (Blueprint $table) {
            $table->boolean('can_edit')->default(true)->after('can_view');
        });
    }

    public function down(): void
    {
        Schema::table('user_page_permissions', function (Blueprint $table) {
            $table->dropColumn('can_edit');
        });
    }
};
