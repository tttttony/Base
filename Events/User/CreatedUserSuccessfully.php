<?php namespace Modules\Base\Events\User;

use Modules\Base\Events\Event;

class CreatedUserSuccessfully extends Event
{

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