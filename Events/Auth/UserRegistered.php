<?php

namespace Modules\Base\Events\Auth;

use Modules\Base\Events\Event;
use Illuminate\Queue\SerializesModels;

/**
 * Class UserRegistered
 * @package App\Events\Frontend\Auth
 */
class UserRegistered extends Event
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