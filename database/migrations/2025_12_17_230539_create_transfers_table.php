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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number')->unique();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('from_location_id')->constrained('locations')->onDelete('cascade');
            $table->foreignId('to_location_id')->constrained('locations')->onDelete('cascade');
            $table->decimal('quantity', 15, 4);
            $table->enum('status', ['pending', 'in_transit', 'completed', 'cancelled'])->default('pending');
            $table->date('transfer_date');
            $table->date('received_date')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['batch_id', 'status']);
            $table->index(['from_location_id', 'to_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
