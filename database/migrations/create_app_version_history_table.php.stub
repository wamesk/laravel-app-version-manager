<?php

declare(strict_types = 1);

use App\Models\User;
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
        $statuses = [
            VersionStatus::CURRENT->toDB(),
            VersionStatus::OLDER->toDB(),
            VersionStatus::DEPRECATED->toDB(),
        ];

        Schema::create('app_version_history', function (Blueprint $table) use ($statuses): void {
            $table->foreignIdFor(model: User::class, column: 'updated_by_user_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->enum(column: 'old_status', allowed: $statuses);
            $table->enum(column: 'new_status', allowed: $statuses);
            $table->timestamp(column: 'created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_version_history');
    }
};
