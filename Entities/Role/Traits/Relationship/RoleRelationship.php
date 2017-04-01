<?php namespace Modules\Base\Entities\Role\Traits\Relationship;

/**
 * Class RoleRelationship
 * @package Modules\Base\Entities\Role\Traits\Relationship
 */
trait RoleRelationship
{
    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), config('base.assigned_roles_table'), 'role_id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function permissions()
    {
        return $this->belongsToMany(config('base.permission'), config('base.permission_role_table'), 'role_id', 'permission_id')
            ->orderBy('display_name', 'asc');
    }
}