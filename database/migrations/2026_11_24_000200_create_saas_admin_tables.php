<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_admin');
            $table->timestamp('suspended_at')->nullable()->after('last_login_at');
        });

        Schema::table('cafes', function (Blueprint $table) {
            $table->timestamp('paid_until')->nullable()->after('trial_ends_at');
            $table->text('admin_notes')->nullable()->after('paid_until');
        });

        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('key', 30)->unique();
            $table->string('name', 60);
            $table->string('tagline')->nullable();
            $table->decimal('price', 10, 2)->default(0);       // per month
            $table->string('currency', 10)->default('ETB');
            $table->unsignedInteger('max_tables')->nullable();
            $table->json('features')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->string('plan', 30);
            $table->unsignedSmallInteger('months')->default(1);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('ETB');
            $table->string('method', 20)->default('bank');      // bank, telebirr, cbe_birr, cash, other
            $table->string('reference', 120)->nullable();
            $table->string('receipt')->nullable();
            $table->string('status', 20)->default('pending');   // pending, approved, rejected
            $table->timestamp('period_start')->nullable();
            $table->timestamp('period_end')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->string('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'approved_at']);
        });

        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('cafe_id')->nullable()->index();
            $table->string('action', 80);
            $table->json('details')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->string('key', 80)->primary();
            $table->text('value')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
        Schema::dropIfExists('admin_logs');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('plans');
        Schema::table('cafes', fn (Blueprint $t) => $t->dropColumn(['paid_until', 'admin_notes']));
        Schema::table('users', fn (Blueprint $t) => $t->dropColumn(['last_login_at', 'suspended_at']));
    }
};
