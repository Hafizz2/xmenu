<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->boolean('is_admin')->default(false)->after('password');
        });

        Schema::create('cafes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('slug', 60)->unique();
            $table->string('name');
            $table->json('tagline')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover')->nullable();
            $table->string('phone', 40)->nullable();
            $table->string('address')->nullable();
            $table->string('map_url', 500)->nullable();
            $table->string('wifi_name')->nullable();
            $table->string('wifi_password')->nullable();
            $table->json('socials')->nullable();
            $table->string('currency', 10)->default('ETB');
            $table->boolean('vat_included')->default(true);
            $table->decimal('service_charge_pct', 5, 2)->default(0);
            $table->string('default_lang', 5)->default('en');
            $table->json('theme')->nullable();
            $table->boolean('games_enabled')->default(true);
            $table->json('disabled_games')->nullable();
            $table->json('qr_settings')->nullable();
            $table->string('plan', 20)->default('trial');
            $table->string('status', 20)->default('trial'); // trial, active, overdue, suspended
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
        });

        Schema::create('opening_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0 = Sunday
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->boolean('closed')->default(false);
            $table->unique(['cafe_id', 'day_of_week']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->string('icon', 16)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->json('name');
            $table->json('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('image')->nullable();
            $table->json('tags')->nullable();
            $table->json('variants')->nullable(); // [{name:{en,am}, price}]
            $table->json('addons')->nullable();   // [{name:{en,am}, price}]
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('visible')->default(true);
            $table->boolean('sold_out')->default(false);
            $table->timestamps();
            $table->index(['cafe_id', 'category_id']);
        });

        Schema::create('payment_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20);
            $table->string('provider')->nullable();
            $table->string('account_name')->nullable();
            $table->string('account_number', 80);
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('visible')->default(true);
            $table->timestamps();
        });

        Schema::create('cafe_tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->string('number', 20);
            $table->timestamps();
            $table->unique(['cafe_id', 'number']);
        });

        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 60)->unique();
            $table->json('name');
            $table->string('source_url', 500);
            $table->boolean('hosted')->default(true);
            $table->string('license', 60)->nullable();
            $table->unsignedTinyInteger('players')->default(2);
            $table->string('mode', 30)->default('pass_and_play');
            $table->unsignedInteger('size_kb')->default(0);
            $table->string('emoji', 16)->nullable();
            $table->string('color', 9)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cafe_id')->constrained()->cascadeOnDelete();
            $table->string('table_no', 20)->nullable();
            $table->string('type', 30);
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('game_id')->nullable();
            $table->string('value')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['cafe_id', 'type', 'created_at']);
        });
    }

    public function down(): void
    {
        foreach (['events', 'games', 'cafe_tables', 'payment_accounts', 'items', 'categories', 'opening_hours', 'cafes'] as $t) {
            Schema::dropIfExists($t);
        }
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['phone', 'is_admin']));
    }
};
