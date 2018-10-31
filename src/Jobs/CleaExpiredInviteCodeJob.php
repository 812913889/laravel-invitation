<?php

namespace Ariby\LaravelInvitation\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Artisan;

/**
 * 刪除過期的邀請碼
 *
 * Class CleaExpiredLaravelInvitationJob
 * @package Ariby\LaravelInvitation\Jobs
 */
class CleaExpiredLaravelInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Artisan::call('routine-clear:clear-expired-invite-codes');
    }
}