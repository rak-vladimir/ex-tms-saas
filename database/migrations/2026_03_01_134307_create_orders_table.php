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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('customer_name');
            $table->string('phone', 50);
            $table->text('pickup_address');
            $table->text('delivery_address');
            $table->dateTime('delivery_date');
            $table->enum('status', OrderStatus::values())
                ->default(OrderStatus::NEW->value);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['tenant_id', 'external_id']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['tenant_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
