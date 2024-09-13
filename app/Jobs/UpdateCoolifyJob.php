<?php

namespace App\Jobs;

use App\Actions\Server\UpdateOh2Bees;
use App\Models\InstanceSettings;
use App\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeEncrypted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UpdateOh2BeesJob implements ShouldBeEncrypted, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600;

    public function handle(): void
    {
        try {
            CheckForUpdatesJob::dispatchSync();
            $settings = InstanceSettings::get();
            if (! $settings->new_version_available) {
                Log::info('No new version available. Skipping update.');

                return;
            }

            $server = Server::findOrFail(0);
            if (! $server) {
                Log::error('Server not found. Cannot proceed with update.');

                return;
            }

            Log::info('Starting Oh2Bees update process...');
            UpdateOh2Bees::run(false); // false means it's not a manual update

            $settings->update(['new_version_available' => false]);
            Log::info('Oh2Bees update completed successfully.');

        } catch (\Throwable $e) {
            Log::error('UpdateOh2BeesJob failed: '.$e->getMessage());
            // Consider implementing a notification to administrators
        }
    }
}
