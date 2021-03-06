<?php namespace Modules\Base\Entities\Permission\Traits\Relationship;

/**
 * Class PermissionDependencyRelationship
 * @package Modules\Base\Entities\Permission\Traits\Relationship
 */
trait PermissionDependencyRelationship
{
    /**
     * @return mixed
     */
    public function permission()
    {
        return $this->hasOne(config('base.permission'), 'id', 'dependency_id');
    }
}