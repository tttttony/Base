<?php namespace Modules\Base\Entities\User;

use DB;
use Illuminate\Database\Eloquent\SoftDeletes;

use Modules\Base\Entities\User\Traits\UserAccess;
use App\User as Authenticatable;
use Modules\Base\Entities\User\Traits\Attribute\UserAttribute;
use Modules\Base\Entities\User\Traits\Relationship\UserRelationship;

/**
 * Class User
 * @package Modules\Base\Entities\User
 */
class User extends Authenticatable
{

    use SoftDeletes, UserAccess, UserAttribute, UserRelationship;

    /**
     * @var array
     */
    protected $dates = ['deleted_at'];
}
