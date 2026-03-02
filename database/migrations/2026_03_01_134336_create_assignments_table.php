<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('courier_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->timestamp('assigned_at')->useCurrent();

            $table->unique(['tenant_id', 'order_id']);
            $table->index('tenant_id');
            $table->index(['tenant_id', 'courier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
