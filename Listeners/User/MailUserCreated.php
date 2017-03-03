<?php namespace Modules\Base\Listeners\User;

use Modules\Base\Events\User\CreatedUserSuccessfully;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
#use Modules\Base\Repositories\User\UserContract as FrontendUserContract;

/**
 * Class UserLoggedInListener
 * @package Modules\Base\Listeners\User
 */
class MailUserCreated implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event handler.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  UserLoggedIn $event
     * @return void
     */
    public function handle(CreatedUserSuccessfully $event)
    {
        //Send confirmation email if requested
        if (isset($event->user->confirmation_email) && $event->user->confirmed == 0) {
            //$this->frontend_user->sendConfirmationEmail($event->user->id);
        }
    }
}