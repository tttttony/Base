<?php namespace Modules\Base\Entities\Permission\Traits\Relationship;

/**
 * Class PermissionRelationship
 * @package Modules\Base\Entities\Permission\Traits\Relationship
 */
trait PermissionRelationship
{
    /**
     * @return mixed
     */
    public function roles()
    {
        return $this->belongsToMany(config('base.role'), config('base.permission_role_table'), 'permission_id', 'role_id');
    }

    /**
     * @return mixed
     */
    public function group()
    {
        return $this->belongsTo(config('base.group'), 'group_id');
    }

    /**
     * @return mixed
     */
    public function users()
    {
        return $this->belongsToMany(config('auth.providers.users.model'), config('base.permission_user_table'), 'permission_id', 'user_id');
    }

    /**
     * @return mixed
     */
    public function dependencies()
    {
        return $this->hasMany(config('base.dependency'), 'permission_id', 'id');
    }
}