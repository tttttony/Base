<?php namespace Modules\Base\Entities\Permission;

use Modules\Base\Entities\Permission\Traits\Attribute\PermissionAttribute;
use Modules\Base\Entities\Permission\Traits\Relationship\PermissionRelationship;
use Modules\Base\Entities\BaseEntity;

/**
 * Class Permission
 * @package Modules\Base\Entities\Permission
 */
class Permission extends BaseEntity
{
    use PermissionRelationship, PermissionAttribute;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table;

    /**
     * The attributes that are not mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     *
     */
    public function __construct()
    {
        $this->table = config('base.permissions_table');
    }
}