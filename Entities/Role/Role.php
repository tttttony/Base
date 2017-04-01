<?php

namespace Modules\Base\Entities\Role;

use Modules\Base\Entities\BaseEntity;
use Modules\Base\Entities\Role\Traits\RoleAccess;
use Modules\Base\Entities\Role\Traits\Attribute\RoleAttribute;
use Modules\Base\Entities\Role\Traits\Relationship\RoleRelationship;

/**
 * Class Role
 * @package Modules\Base\Entities\Role
 */
class Role extends BaseEntity
{
    use RoleAccess, RoleAttribute, RoleRelationship;

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
        $this->table = config('base.roles_table');
    }
}
