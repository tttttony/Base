<?php namespace Modules\Base\Entities\Permission;

use Modules\Base\Entities\BaseEntity;
use Modules\Base\Entities\Permission\Traits\Attribute\PermissionGroupAttribute;
use Modules\Base\Entities\Permission\Traits\Relationship\PermissionGroupRelationship;

/**
 * Class PermissionGroup
 * @package Modules\Base\Entities\Permission
 */
class PermissionGroup extends BaseEntity
{
    use PermissionGroupRelationship, PermissionGroupAttribute;

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
        $this->table = config('access.permission_group_table');
    }
}