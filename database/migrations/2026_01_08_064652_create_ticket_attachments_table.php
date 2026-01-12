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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->string('filename'); // Original filename
            $table->string('cloudinary_public_id'); // Cloudinary public ID
            $table->string('cloudinary_url'); // Cloudinary URL
            $table->string('cloudinary_secure_url'); // Cloudinary secure URL
            $table->string('file_type'); // MIME type
            $table->integer('file_size'); // File size in bytes
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
