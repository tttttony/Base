<?php

namespace Modules\Base\Http\Controllers\Access\Role;

use Modules\Base\Http\Controllers\Controller;
use Modules\Base\Repositories\RoleRepository;
use Modules\Base\Http\Requests\Access\Role\EditRoleRequest;
use Modules\Base\Http\Requests\Access\Role\StoreRoleRequest;
use Modules\Base\Http\Requests\Access\Role\CreateRoleRequest;
use Modules\Base\Http\Requests\Access\Role\DeleteRoleRequest;
use Modules\Base\Http\Requests\Access\Role\UpdateRoleRequest;
use Modules\Base\Repositories\PermissionRepository;
use Modules\Base\Repositories\PermissionGroupRepository;

/**
 * Class RoleController
 * @package Modules\Base\Http\Controllers\Access
 */
class RoleController extends Controller
{
    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * @var PermissionRepository
     */
    protected $permissions;

    /**
     * @param RoleRepository       $roles
     * @param PermissionRepository $permissions
     */
    public function __construct(
        RoleRepository $roles,
        PermissionRepository $permissions
    )
    {
        $this->roles       = $roles;
        $this->permissions = $permissions;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('access.roles.index')
            ->withRoles($this->roles->getRolesPaginated(50));
    }

    /**
     * @param  PermissionGroupRepository    $group
     * @param  CreateRoleRequest            $request
     * @return mixed
     */
    public function create(PermissionGroupRepository $group, CreateRoleRequest $request)
    {
        return view('access.roles.create')
            ->withGroups($group->getAllGroups())
            ->withPermissions($this->permissions->getUngroupedPermissions());
    }

    /**
     * @param  StoreRoleRequest $request
     * @return mixed
     */
    public function store(StoreRoleRequest $request)
    {
        $this->roles->create($request->all());
        return redirect()->route('admin.access.roles.index')->withFlashSuccess(trans('alerts.backend.roles.created'));
    }

    /**
     * @param  $id
     * @param  PermissionGroupRepository    $group
     * @param  EditRoleRequest              $request
     * @return mixed
     */
    public function edit($id, PermissionGroupRepository $group, EditRoleRequest $request)
    {
        $role = $this->roles->findOrThrowException($id, true);
        return view('access.roles.edit')
            ->withRole($role)
            ->withRolePermissions($role->permissions->pluck('id')->all())
            ->withGroups($group->getAllGroups())
            ->withPermissions($this->permissions->getUngroupedPermissions());
    }

    /**
     * @param  $id
     * @param  UpdateRoleRequest $request
     * @return mixed
     */
    public function update($id, UpdateRoleRequest $request)
    {
        $this->roles->update($id, $request->all());
        return redirect()->route('admin.access.roles.index')->withFlashSuccess(trans('alerts.backend.roles.updated'));
    }

    /**
     * @param  $id
     * @param  DeleteRoleRequest $request
     * @return mixed
     */
    public function destroy($id, DeleteRoleRequest $request)
    {
        $this->roles->destroy($id);
        return redirect()->route('admin.access.roles.index')->withFlashSuccess(trans('alerts.backend.roles.deleted'));
    }
}
