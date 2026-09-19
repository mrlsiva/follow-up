<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('followup_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('followup_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('type', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('followup_attachments');
    }
};
