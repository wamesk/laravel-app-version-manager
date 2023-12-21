<?php

declare(strict_types = 1);

namespace Wame\LaravelAppVersionManager\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Wame\LaravelAppVersionManager\Events\AppVersionUpdatedEvent;
use Wame\LaravelAppVersionManager\Models\AppVersion;

class SaveAppVersionHistoryJob implements ShouldQueue
{
    use Dispatchable;
    use Queueable;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        public AppVersion $appVersion,
        public string $oldStatus,
        public string $newStatus,
    ) {
    }

    /**
     * Handle the event.
     */
    public function handle(AppVersion $appVersion): void
    {
        DB::table(table: 'app_version_history')
            ->insert([
                'updated_by_user_id' => auth()->user()?->id,
                'old_status' => $this->oldStatus,
                'new_status' => $this->newStatus,
                'created_at' => now(),
            ]);
    }
}
