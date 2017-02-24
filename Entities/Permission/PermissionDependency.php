<?php namespace Modules\Base\Entities\Permission;

use Modules\Base\Entities\BaseEntity;
use Modules\Base\Entities\Permission\Traits\Relationship\PermissionDependencyRelationship;

/**
 * Class PermissionDependency
 * @package Modules\Base\Entities\Permission
 */
class PermissionDependency extends BaseEntity
{
    use PermissionDependencyRelationship;

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
        $this->table = config('access.permission_dependencies_table');
    }
}