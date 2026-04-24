<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_at')->index();
            $table->date('period_month')->index()
                ->comment('Hansı ayın ödənişidir, həmişə ayın 1-i (məs. 2026-04-01)');
            $table->boolean('is_prorata')->default(false);
            $table->string('method')->nullable()->comment('nağd, kart, köçürmə və s.');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['enrollment_id', 'period_month']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
