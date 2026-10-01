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
        Schema::create('failed_orders', function (Blueprint $table) {
            $table->id();
            $table->string('session_id')->nullable()->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable()->index();
            $table->text('shipping_address')->nullable();
            $table->string('delivery_zone')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('customer_note')->nullable();
            $table->json('cart_items')->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('shipping_charge', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->string('ip_address')->nullable()->index();
            $table->text('user_agent')->nullable();
            $table->string('status')->default('abandoned'); // abandoned, attempted, contacted, recovered
            $table->string('failure_reason')->nullable();
            $table->boolean('is_recovered')->default(false)->index();
            $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->text('contact_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('failed_orders');
    }
};
