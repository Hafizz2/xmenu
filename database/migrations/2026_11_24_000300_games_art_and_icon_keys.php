<?php

use App\Support\Icons;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('games', function (Blueprint $table) {
            $table->json('description')->nullable()->after('name');
            $table->string('art', 30)->nullable()->after('emoji');
            $table->boolean('has_ai')->default(false)->after('mode');
        });

        // Category icons are now SVG keys; clear anything else (old emoji).
        DB::table('categories')->whereNotIn('icon', array_keys(Icons::CATEGORY))->update(['icon' => null]);
    }

    public function down(): void
    {
        Schema::table('games', fn (Blueprint $t) => $t->dropColumn(['description', 'art', 'has_ai']));
    }
};
