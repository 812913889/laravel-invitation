<?php

namespace Ariby\LaravelInvitation;

use Ariby\LaravelInvitation\Exceptions\LaravelInvitationException;
use Ariby\LaravelInvitation\Exceptions\ExpiredLaravelInvitation;
use Ariby\LaravelInvitation\Exceptions\LaravelInvitationUsedOverMaxException;
use Ariby\LaravelInvitation\Exceptions\NotYourLaravelInvitationException;
use Ariby\LaravelInvitation\Exceptions\NotFoundLaravelInvitationException;
use Ariby\LaravelInvitation\Models\InviteCode;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class LaravelInvitation
{
    public $error = '';

    /**
     * 使用邀请码(會將使用次數 + 1)
     *
     * @param             $code
     * @param string|null $belongTo
     *
     * @throws ExpiredLaravelInvitation
     * @throws LaravelInvitationUsedOverMaxException
     * @throws NotFoundLaravelInvitationException
     * @throws NotYourLaravelInvitationException
     */
    public function redeem($code, string $belongTo = null)
    {
        $invite = $this->findAndValid($code, $belongTo);

        $invite->increment('uses');
    }

    /**
     * 尋找邀请码並驗證狀態是否可使用
     *
     * @param             $code
     * @param string|null $belongTo
     *
     * @return LaravelInvitation
     * @throws ExpiredLaravelInvitation
     * @throws LaravelInvitationUsedOverMaxException
     * @throws NotFoundLaravelInvitationException
     * @throws NotYourLaravelInvitationException
     */
    protected function findAndValid($code, string $belongTo = null)
    {
        $this->error = '';
        $invite = $this->lookupInvite($code);
        $this->validateInvite($invite, $belongTo);

        return $invite;
    }

    /**
     * 檢查此邀请码是否存在(不會增加使用次數)
     *
     * @param             $code
     * @param string|null $belongTo
     *
     * @return bool
     */
    public function isLaravelInvitationUsable($code, string $belongTo = null)
    {
        try {
            $this->findAndValid($code, $belongTo);
            return true;
        } catch (LaravelInvitationException $e) {
            $this->error = $e->getMessage();
            return false;
        }
    }

    /**
     * @param $code
     *
     * @return \Ariby\LaravelInvitation\LaravelInvitation
     * @throws NotFoundLaravelInvitationException
     */
    protected function lookupInvite($code): InviteCode
    {
        try {
            return InviteCode::where('code', '=', $code)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new NotFoundLaravelInvitationException('邀请码「'.$code.'」不存在');
        }
    }

    /**
     * @param \Ariby\LaravelInvitation\LaravelInvitation $invite
     * @param string|null                  $belongTo
     *
     * @throws ExpiredLaravelInvitation
     * @throws LaravelInvitationUsedOverMaxException
     * @throws NotYourLaravelInvitationException
     */
    protected function validateInvite(InviteCode $invite, string $belongTo = null)
    {
        if ($invite->isFull()) {
            throw new LaravelInvitationUsedOverMaxException('邀请码「'.$invite->code.'」已超过最大可使用次数');
        }

        if ($invite->isExpired()) {
            throw new ExpiredLaravelInvitation('邀请码「'.$invite->code.'」已过期');
        }

        if ($invite->isRestricted() && !$invite->isRestrictedFor($belongTo)) {
            throw new NotYourLaravelInvitationException('您沒有此邀请码的使用权限');
        }
    }

    public function generate()
    {
        return app(Generator::class);
    }
}
