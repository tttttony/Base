<?php

namespace Modules\Base\Listeners\Auth;

use Illuminate\Queue\InteractsWithQueue;
use Modules\Base\Events\Auth\UserLoggedIn;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class UserLoggedInListener
 * @package Modules\Base\Listeners\Auth
 */
class UserLoggedInListener implements ShouldQueue
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
     * @param  UserLoggedIn $event
     * @return void
     */
    public function handle(UserLoggedIn $event)
    {
        \Log::info('User Logged In: ' . $event->user->name);
    }
}