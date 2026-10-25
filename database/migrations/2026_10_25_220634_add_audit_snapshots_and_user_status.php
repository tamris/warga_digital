<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('role');
            });
        }

        if (! Schema::hasColumn('cash_ledgers', 'recorded_by_name')) {
            Schema::table('cash_ledgers', function (Blueprint $table) {
                $table->string('recorded_by_name', 100)->nullable()->after('user_id');
            });
        }

        if (! Schema::hasColumn('letters', 'created_by_name')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->string('created_by_name', 100)->nullable()->after('created_by_user_id');
            });
        }

        if (! Schema::hasColumn('house_dues', 'received_by_name')) {
            Schema::table('house_dues', function (Blueprint $table) {
                $table->string('received_by_name', 100)->nullable()->after('received_by_user_id');
            });
        }

        // Backfill existing rows with creator/recorder name
        try {
            $ledgers = DB::table('cash_ledgers')->whereNull('recorded_by_name')->whereNotNull('user_id')->get(['id', 'user_id']);
            foreach ($ledgers as $l) {
                $user = DB::table('users')->where('id', $l->user_id)->first(['nama']);
                if ($user) {
                    DB::table('cash_ledgers')->where('id', $l->id)->update(['recorded_by_name' => $user->nama]);
                }
            }
        } catch (Throwable $e) {
            // ignore
        }

        try {
            $letters = DB::table('letters')->whereNull('created_by_name')->get(['id', 'created_by_user_id', 'nama_ketua_rt']);
            foreach ($letters as $let) {
                $name = null;
                if ($let->created_by_user_id) {
                    $u = DB::table('users')->where('id', $let->created_by_user_id)->first(['nama']);
                    $name = $u?->nama;
                }
                $name = $name ?: $let->nama_ketua_rt;
                if ($name) {
                    DB::table('letters')->where('id', $let->id)->update(['created_by_name' => $name]);
                }
            }
        } catch (Throwable $e) {
            // ignore
        }

        try {
            $dues = DB::table('house_dues')->whereNull('received_by_name')->whereNotNull('received_by_user_id')->get(['id', 'received_by_user_id']);
            foreach ($dues as $d) {
                $u = DB::table('users')->where('id', $d->received_by_user_id)->first(['nama']);
                if ($u) {
                    DB::table('house_dues')->where('id', $d->id)->update(['received_by_name' => $u->nama]);
                }
            }
        } catch (Throwable $e) {
            // ignore
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }

        if (Schema::hasColumn('cash_ledgers', 'recorded_by_name')) {
            Schema::table('cash_ledgers', function (Blueprint $table) {
                $table->dropColumn('recorded_by_name');
            });
        }

        if (Schema::hasColumn('letters', 'created_by_name')) {
            Schema::table('letters', function (Blueprint $table) {
                $table->dropColumn('created_by_name');
            });
        }

        if (Schema::hasColumn('house_dues', 'received_by_name')) {
            Schema::table('house_dues', function (Blueprint $table) {
                $table->dropColumn('received_by_name');
            });
        }
    }
};
