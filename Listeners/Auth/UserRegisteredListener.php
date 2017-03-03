<?php

namespace Modules\Base\Listeners\Auth;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Base\Events\Auth\UserRegistered;

/**
 * Class UserRegisteredListener
 * @package Modules\Base\Listeners\Auth
 */
class UserRegisteredListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  UserRegistered $event
     * @return void
     */
    public function handle(UserRegistered $event)
    {
        if(empty($event->user->profile)) $event->user->load('profile');
        \Log::info('User Registered: ' . $event->user->profile->first . '' . $event->user->profile->last . '<' . $event->user->email . '>');
    }
}
