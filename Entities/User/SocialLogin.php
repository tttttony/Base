<?php namespace Modules\Base\Entities\User;

use Modules\Base\Entities\BaseEntity;

/**
 * Class SocialLogin
 * @package Modules\Base\Entities\User
 */
class SocialLogin extends BaseEntity
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'social_logins';

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}