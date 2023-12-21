<?php

declare(strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Wame\LaravelAppVersionManager\Enums\VersionStatus;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table): void {
            $table->ulid(column: 'id')->primary();
            $table->string(column: 'title');
            $table->enum(column: 'status', allowed: [
                VersionStatus::CURRENT->toDB(),
                VersionStatus::OLDER->toDB(),
                VersionStatus::DEPRECATED->toDB(),
            ]);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
