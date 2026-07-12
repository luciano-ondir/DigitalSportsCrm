<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('sports')) {
            Schema::create('sports', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('code', 50)->nullable()->unique();
                $table->string('slug')->nullable()->unique();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        $now = now();

        foreach ([
            ['name' => 'Finswimming', 'code' => 'FINSWIMMING', 'slug' => 'finswimming'],
            ['name' => 'Freediving', 'code' => 'FREEDIVING', 'slug' => 'freediving'],
            ['name' => 'Underwater Rugby', 'code' => 'UNDERWATER_RUGBY', 'slug' => 'underwater-rugby'],
            ['name' => 'Underwater Hockey', 'code' => 'UNDERWATER_HOCKEY', 'slug' => 'underwater-hockey'],
            ['name' => 'Sport Diving', 'code' => 'SPORT_DIVING', 'slug' => 'sport-diving'],
            ['name' => 'Underwater Orienteering', 'code' => 'UNDERWATER_ORIENTEERING', 'slug' => 'underwater-orienteering'],
            ['name' => 'Spearfishing', 'code' => 'SPEARFISHING', 'slug' => 'spearfishing'],
        ] as $sport) {
            DB::table('sports')->updateOrInsert(
                ['code' => $sport['code']],
                [
                    'name' => $sport['name'],
                    'slug' => $sport['slug'],
                    'description' => null,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('sports');
    }
};
