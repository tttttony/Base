<?php

namespace Modules\Base\Http\Controllers\Access\Permission;

use Modules\Base\Http\Controllers\Controller;
use Modules\Base\Repositories\RoleRepository;
use Modules\Base\Repositories\PermissionRepository;
use Modules\Base\Http\Requests\Access\Permission\EditPermissionRequest;
use Modules\Base\Http\Requests\Access\Permission\CreatePermissionRequest;
use Modules\Base\Http\Requests\Access\Permission\DeletePermissionRequest;
use Modules\Base\Http\Requests\Access\Permission\StorePermissionRequest;
use Modules\Base\Http\Requests\Access\Permission\UpdatePermissionRequest;
use Modules\Base\Repositories\PermissionGroupRepository;

/**
 * Class PermissionController
 * @package Modules\Base\Http\Controllers\Access
 */
class PermissionController extends Controller
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
     * @var PermissionGroupRepository
     */
    protected $groups;

    /**
     * @param RoleRepository            $roles
     * @param PermissionRepository      $permissions
     * @param PermissionGroupRepository $groups
     */
    public function __construct(
        RoleRepository $roles,
        PermissionRepository $permissions,
        PermissionGroupRepository $groups
    )
    {
        $this->roles       = $roles;
        $this->permissions = $permissions;
        $this->groups      = $groups;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('access.roles.permissions.index')
            ->withPermissions($this->permissions->getPermissionsPaginated(50))
            ->withGroups($this->groups->getAllGroups());
    }

    /**
     * @param  CreatePermissionRequest $request
     * @return mixed
     */
    public function create(CreatePermissionRequest $request)
    {
        return view('access.roles.permissions.create')
            ->withGroups($this->groups->getAllGroups(true))
            ->withRoles($this->roles->getAllRoles())
            ->withPermissions($this->permissions->getAllPermissions());
    }

    /**
     * @param  StorePermissionRequest $request
     * @return mixed
     */
    public function store(StorePermissionRequest $request)
    {
        $this->permissions->create($request->except('permission_roles'), $request->only('permission_roles'));
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.created'));
    }

    /**
     * @param  $id
     * @param  EditPermissionRequest $request
     * @return mixed
     */
    public function edit($id, EditPermissionRequest $request)
    {
        $permission = $this->permissions->findOrThrowException($id, true);
        return view('access.roles.permissions.edit')
            ->withPermission($permission)
            ->withPermissionRoles($permission->roles->lists('id')->all())
            ->withGroups($this->groups->getAllGroups(true))
            ->withRoles($this->roles->getAllRoles())
            ->withPermissions($this->permissions->getAllPermissions())
            ->withPermissionDependencies($permission->dependencies->lists('dependency_id')->all());
    }

    /**
     * @param  $id
     * @param  UpdatePermissionRequest $request
     * @return mixed
     */
    public function update($id, UpdatePermissionRequest $request)
    {
        $this->permissions->update($id, $request->except('permission_roles'), $request->only('permission_roles'));
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.updated'));
    }

    /**
     * @param  $id
     * @param  DeletePermissionRequest $request
     * @return mixed
     */
    public function destroy($id, DeletePermissionRequest $request)
    {
        $this->permissions->destroy($id);
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.deleted'));
    }
}
