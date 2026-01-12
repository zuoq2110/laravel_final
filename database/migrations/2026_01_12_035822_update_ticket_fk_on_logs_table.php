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
        Schema::table('logs', function (Blueprint $table) {
            // 1. Drop foreign key constraint cũ
            $table->dropForeign(['ticket_id']);

            // 2. Modify column để cho phép null
            $table->unsignedBigInteger('ticket_id')->nullable()->change();
            
            // 3. Add foreign key constraint mới với nullOnDelete
            $table->foreign('ticket_id')->references('id')->on('tickets')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            // 1. Drop foreign key mới
            $table->dropForeign(['ticket_id']);

            // 2. Modify column về không nullable
            $table->unsignedBigInteger('ticket_id')->nullable(false)->change();
            
            // 3. Add lại foreign key cũ (cascade delete)
            $table->foreign('ticket_id')->references('id')->on('tickets')->cascadeOnDelete();
        });
    }
};
