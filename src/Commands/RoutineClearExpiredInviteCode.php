<?php

namespace Ariby\LaravelInvitation\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;

class RoutineClearExpiredLaravelInvitation extends Command
{
    // 命令名稱
    protected $signature = 'routine-clear:clear-expired-invite-codes';

    // 說明文字
    protected $description = '刪除已經過期的邀請碼';

    public function __construct()
    {
        parent::__construct();
    }

    // Console 執行的程式
    public function handle()
    {
        logger('---Clear Expired Invite Code Job START at' . date("Y-m-d H:i:s") . '---');

        $count = Invite::useless()->count();
        Invite::useless()->delete();
        $this->info('Successfully deleted ' . $count . ' expired invite codes from the database.');

        logger('---Clear Expired Invite Code Job END at' . date("Y-m-d H:i:s") . '---');
    }
}