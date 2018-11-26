<?php

namespace Ariby\LaravelInvitation;

use Ariby\LaravelInvitation\Models\InviteCode;
use Carbon\Carbon;
use Illuminate\Support\Str;

class Generator
{
    protected $amount = 1;
    protected $uses = 0;
    protected $code = null;
    protected $max = null;
    protected $status = 'enabled';
    protected $for = null;
    protected $type = null;
    protected $belongTo = null;
    protected $madeBy = null;
    protected $expiry = null;

    /**
     * @param int $amount
     *
     * @return $this
     */
    public function times(int $amount = 1)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @param int $uses
     *
     * @return $this
     */
    public function uses(int $uses = 0)
    {
        $this->uses = $uses;

        return $this;
    }

    /**
     * @param string $for
     *
     * @return $this
     */
    public function for(string $for)
    {
        $this->for = $for;

        return $this;
    }

    /**
     * @param int $max
     *
     * @return $this
     */
    public function max(int $max)
    {
        $this->max = $max;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function type(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function status(string $status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param string $belongTo
     *
     * @return $this
     */
    public function belongTo(string $belongTo)
    {
        $this->belongTo = $belongTo;

        return $this;
    }

    /**
     * @param string $madeBy
     *
     * @return $this
     */
    public function madeBy(string $madeBy)
    {
        $this->madeBy = $madeBy;

        return $this;
    }

    /**
     * @param \Carbon\Carbon $date
     *
     * @return $this
     */
    public function expiresOn(Carbon $date)
    {
        $this->expiry = $date;

        return $this;
    }

    /**
     * @param int $days
     *
     * @return $this
     */
    public function expiresIn($days = 14)
    {
        $this->expiry = Carbon::now(config('app.timezone'))->addDays($days)->endOfDay();

        return $this;
    }

    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return \Ariby\LaravelInvitation\Models\InviteCode
     */
    protected function build(): InviteCode
    {
        $invite = new InviteCode();
        if ($this->amount == 1 && !is_null($this->code)) {
            $invite->code = $this->code;
        } else {
            $invite->code = Str::random(config('laravel_invitation.code_length'));
        }

        $invite->status = 'enabled';
        $invite->for = $this->for;
        $invite->belong_to = $this->belongTo;
        $invite->made_by = $this->madeBy;
        $invite->uses = $this->uses;
        $invite->max = $this->max;
        $invite->type = $this->type;
        $invite->valid_until = $this->expiry;

        return $invite;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function make()
    {
        $invites = collect();

        for ($i = 0; $i < $this->amount; $i++) {
            $invite = $this->build();

            $invites->push($invite);

            $invite->save();
        }

        return $invites;
    }
}
