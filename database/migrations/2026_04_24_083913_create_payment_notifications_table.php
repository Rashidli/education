<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')->constrained()->cascadeOnDelete();
            $table->string('channel')->comment('database, sms, whatsapp və s.');
            $table->string('status')->default('pending')->index()->comment('pending, sent, failed');
            $table->date('due_date');
            $table->integer('days_before');
            $table->text('payload')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->unique(['enrollment_id', 'channel', 'due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_notifications');
    }
};
