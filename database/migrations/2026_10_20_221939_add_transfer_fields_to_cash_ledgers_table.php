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
        Schema::table('cash_ledgers', function (Blueprint $table) {
            if (! Schema::hasColumn('cash_ledgers', 'related_ledger_id')) {
                $table->foreignId('related_ledger_id')->nullable()->after('house_due_id')->constrained('cash_ledgers')->nullOnDelete();
            }
            if (! Schema::hasColumn('cash_ledgers', 'status_transfer')) {
                $table->string('status_transfer', 30)->nullable()->after('related_ledger_id');
            }
            if (! Schema::hasColumn('cash_ledgers', 'accepted_by_user_id')) {
                $table->foreignId('accepted_by_user_id')->nullable()->after('status_transfer')->constrained('users')->nullOnDelete();
            }
            if (! Schema::hasColumn('cash_ledgers', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('accepted_by_user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_ledgers', function (Blueprint $table) {
            if (Schema::hasColumn('cash_ledgers', 'accepted_by_user_id')) {
                $table->dropForeign(['accepted_by_user_id']);
                $table->dropColumn('accepted_by_user_id');
            }
            if (Schema::hasColumn('cash_ledgers', 'status_transfer')) {
                $table->dropColumn('status_transfer');
            }
            if (Schema::hasColumn('cash_ledgers', 'accepted_at')) {
                $table->dropColumn('accepted_at');
            }
        });
    }
};
