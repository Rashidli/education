<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('group_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->date('joined_at');
            $table->date('left_at')->nullable();
            $table->date('next_due_date')->nullable()->index();
            $table->decimal('first_month_amount', 10, 2)->nullable()
                ->comment('Pro-rata hesablanmış ilk ay məbləği');
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();

            $table->index(['student_id', 'is_active']);
            $table->index(['group_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
