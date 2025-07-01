<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wame\LaravelAppVersionManager\Enums\Platform;

return new class() extends Migration
{
    public function up(): void
    {
        Schema::table('app_versions', function (Blueprint $table): void {
            $table->enum('platform', collect(Platform::cases())->pluck('value')->toArray())->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('app_versions', function (Blueprint $table): void {
            $table->dropColumn('platform');
        });
    }
};
