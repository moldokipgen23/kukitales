<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('donation_campaigns')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('donor_name');
            $table->string('donor_email')->nullable();
            $table->string('donor_phone')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 4)->default('INR');
            $table->string('payment_method')->default('upi'); // upi, bank_transfer, razorpay, stripe, paypal, cash, other
            $table->string('payment_reference')->nullable(); // UTR / txn id / receipt number
            $table->string('status')->default('pending'); // pending, confirmed, failed, refunded
            $table->text('message')->nullable();
            $table->boolean('is_anonymous')->default(false);
            $table->boolean('show_on_wall')->default(true);
            $table->timestamps();

            $table->index(['campaign_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
