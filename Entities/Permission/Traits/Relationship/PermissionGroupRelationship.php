<?php namespace Modules\Base\Entities\Permission\Traits\Relationship;

/**
 * Class PermissionGroupRelationship
 * @package Modules\Base\Entities\Permission\Traits\Relationship
 */
trait PermissionGroupRelationship
{
    /**
     * @return mixed
     */
    public function children()
    {
        return $this->hasMany(config('base.group'), 'parent_id', 'id')->orderBy('sort', 'asc');
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->hasMany(config('base.permission'), 'group_id')->orderBy('sort', 'asc');
    }
}