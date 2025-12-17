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
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['in', 'out', 'adjustment']); // in: receipt, out: issue, adjustment: correction
            $table->enum('reference_type', ['supplier', 'production', 'transfer', 'sale', 'adjustment', 'other'])->nullable();
            $table->unsignedBigInteger('reference_id')->nullable(); // polymorphic reference
            $table->decimal('quantity', 15, 4);
            $table->decimal('unit_cost', 15, 4)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('movement_date');
            $table->timestamps();

            $table->index(['batch_id', 'location_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
