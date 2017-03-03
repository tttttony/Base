<?php namespace Modules\Base\Listeners\User;

use Log;
use Modules\Base\Events\User\CreatedUserSuccessfully;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class UserLoggedInListener
 * @package Modules\Base\Listeners\Auth
 */
class LogUserCreated implements ShouldQueue
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
    public function handle(CreatedUserSuccessfully $event)
    {
        Log::info('User Created: ' . $event->user->profile->first . ' ' . $event->user->profile->last . ' <' . $event->user->email . '>');
    }
}