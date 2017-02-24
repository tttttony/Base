<?php

namespace Modules\Base\Events\Auth;

use Modules\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserLoggedIn
 * @package App\Events\Frontend\Auth
 */
class UserLoggedIn extends Event
{
    use SerializesModels;

    /**
     * @var $user
     */
    public $user;

    /**
     * @param $user
     */
    public function __construct($user)
    {
        $this->user = $user;
    }
}