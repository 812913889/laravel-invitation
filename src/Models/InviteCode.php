<?php

namespace Ariby\LaravelInvitation\Models;

use Ariby\Ulid\HasUlid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 邀請碼
 *
 * Class InviteCode
 * @property string id
 * @property string code 邀請碼
 * @property string status 邀請碼的開放狀態
 * @property string type 邀請碼種類
 * @property string for 邀請碼的專屬使用者 (null 表示都可以使用)
 * @property string belong_to 邀請碼的擁有者
 * @property string made_by 邀請碼的製作者
 * @property int max 邀請碼的最大使用次數
 * @property int uses 邀請碼的已使用次數
 * @property datetime valid_until 邀請碼的有效期限
 * @property datetime created_at
 * @property datetime updated_at
 */
class InviteCode extends Model
{
    use HasUlid;

    protected $dates = [
        'valid_until'
    ];

    protected $fillable = [
        'code',
        'status',
        'type',
        'for',
        'belong_to',
        'made_by',
        'max',
        'uses',
        'valid_until'
    ];

    public function __construct(array $attributes = [ ])
    {
        $this->table = config('laravel_invitation.invite_table_name', 'invites');
        parent::__construct($attributes);
    }

    /**
     * Is the invite expired.
     *
     * @return bool
     */
    public function isExpired()
    {
        if (is_null($this->valid_until)) {
            return false;
        }

        return $this->valid_until->isPast(); // carbon 內建函式
    }

    /**
     * Is the invite enabled.
     *
     * @return bool
     */
    public function isEnabled()
    {
        if ($this->status == 'enabled') {
            return true;
        }else{
            return false;
        }
    }

    /**
     * Is the invite used times over limit.
     *
     * @return bool
     */
    public function isFull()
    {
        if (is_null($this->max)) {
            return false;
        }

        return $this->uses >= $this->max;
    }

    /**
     * Is the invite restricted to a user.
     *
     * @return bool
     */
    public function isRestricted()
    {
        return !is_null($this->for);
    }


    /**
     * Is the invite restricted for a particular user.
     *
     * @param string $userId
     *
     * @return bool
     */
    public function isRestrictedFor($userId)
    {
        return $userId == $this->for;
    }

    /**
     * Can the invite be used anymore.
     *
     * @return bool
     */
    public function isUseless()
    {
        return $this->isExpired() || $this->isFull() || $this->isEnabled();
    }

    /**
     * Scope a query to only include expired invites.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('valid_until', '<', Carbon::now(config('app.timezone')));
    }

    /**
     * Scope a query to only include full invites.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFull($query)
    {
        return $query->whereNotNull('max')->whereRaw('uses = max');
    }

    /**
     * Scope a query to only include useless invites.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUseless($query)
    {
        return $query
            ->where(function($q) {
                $this->scopeExpired($q);
            })
            ->orWhere(function($q) {
                $this->scopeFull($q);
            })
            ;
    }
}
