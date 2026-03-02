<?php

use App\Enums\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->foreignId('order_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('status', OrderStatus::values())
                ->default(OrderStatus::NEW->value);
            $table->json('meta')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['tenant_id', 'order_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
    }
};
