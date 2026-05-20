<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaint_status_histories', function (Blueprint $table) {
            // Drop the existing FK constraint first, then re-add with nullOnDelete
            $table->dropForeign(['changed_by']);
            $table->foreignId('changed_by')->nullable()->change();
            $table->foreign('changed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('complaint_status_histories', function (Blueprint $table) {
            $table->dropForeign(['changed_by']);
            $table->foreignId('changed_by')->nullable(false)->change();
            $table->foreign('changed_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
