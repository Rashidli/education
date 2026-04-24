<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('teacher_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('paid_at')->index();
            $table->string('method')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->comment('kim ödəyib');
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('deleted_by')->nullable();

            $table->index(['teacher_id', 'paid_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_payouts');
    }
};
