<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->foreignId('manager_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('duration');
            $table->string('result');
            $table->timestamps();

            $table->index('lead_id');
            $table->index('manager_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calls');
    }
};
