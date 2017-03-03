<?php

namespace Modules\Base\Repositories\Eloquent;

use Modules\Base\Entities\Permission\PermissionDependency;
use Modules\Base\Repositories\PermissionDependencyRepository;

/**
 * Class EloquentPermissionDependencyRepository
 * @package Modules\Base\Repositories\Permission\Dependency
 */
class EloquentPermissionDependencyRepository implements PermissionDependencyRepository
{
    /**
     * @param  $permission_id
     * @param  $dependency_id
     * @return mixed
     */
    public function create($permission_id, $dependency_id)
    {
        $dependency                = new PermissionDependency;
        $dependency->permission_id = $permission_id;
        $dependency->dependency_id = $dependency_id;
        return $dependency->save();
    }

    /**
     * @param  $permission_id
     * @return mixed
     */
    public function clear($permission_id)
    {
        return PermissionDependency::where('permission_id', $permission_id)
            ->delete();
    }
}