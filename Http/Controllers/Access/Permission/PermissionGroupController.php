<?php

namespace Modules\Base\Http\Controllers\Access\Permission;

use Modules\Base\Http\Controllers\Controller;
use Modules\Base\Repositories\PermissionGroupRepository;
use Modules\Base\Http\Requests\Access\Permission\Group\SortPermissionGroupRequest;
use Modules\Base\Http\Requests\Access\Permission\Group\EditPermissionGroupRequest;
use Modules\Base\Http\Requests\Access\Permission\Group\StorePermissionGroupRequest;
use Modules\Base\Http\Requests\Access\Permission\Group\CreatePermissionGroupRequest;
use Modules\Base\Http\Requests\Access\Permission\Group\DeletePermissionGroupRequest;
use Modules\Base\Http\Requests\Access\Permission\Group\UpdatePermissionGroupRequest;

/**
 * Class PermissionGroupController
 * @package Modules\Base\Http\Controllers\Access
 */
class PermissionGroupController extends Controller
{
    /**
     * @var PermissionGroupRepository
     */
    protected $groups;

    /**
     * @param PermissionGroupRepository $groups
     */
    public function __construct(PermissionGroupRepository $groups)
    {
        $this->groups = $groups;
    }

    /**
     * @param  CreatePermissionGroupRequest $request
     * @return \Illuminate\View\View
     */
    public function create(CreatePermissionGroupRequest $request)
    {
        return view('access.roles.permissions.groups.create');
    }

    /**
     * @param  StorePermissionGroupRequest $request
     * @return mixed
     */
    public function store(StorePermissionGroupRequest $request)
    {
        $this->groups->store($request->all());
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.groups.created'));
    }

    /**
     * @param  $id
     * @param  EditPermissionGroupRequest $request
     * @return mixed
     */
    public function edit($id, EditPermissionGroupRequest $request)
    {
        return view('access.roles.permissions.groups.edit')
            ->withGroup($this->groups->find($id));
    }

    /**
     * @param  $id
     * @param  UpdatePermissionGroupRequest $request
     * @return mixed
     */
    public function update($id, UpdatePermissionGroupRequest $request)
    {
        $this->groups->update($id, $request->all());
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.groups.created'));
    }

    /**
     * @param  $id
     * @param  DeletePermissionGroupRequest $request
     * @return mixed
     */
    public function destroy($id, DeletePermissionGroupRequest $request)
    {
        $this->groups->destroy($id);
        return redirect()->route('admin.access.roles.permissions.index')->withFlashSuccess(trans('alerts.backend.permissions.groups.deleted'));
    }

    /**
     * @param  SortPermissionGroupRequest      $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateSort(SortPermissionGroupRequest $request)
    {
        $this->groups->updateSort($request->get('data'));
        return response()->json(['status' => 'OK']);
    }
}
