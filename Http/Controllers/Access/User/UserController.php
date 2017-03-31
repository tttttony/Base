<?php namespace Modules\Base\Http\Controllers\Access\User;

use DB;
use Modules\Base\Http\Controllers\Controller;

use Modules\Base\Repositories\PermissionGroupRepository;
use Modules\Base\Repositories\UserRepository;
use Modules\Base\Repositories\RoleRepository;
use Modules\Base\Repositories\PermissionRepository;

use Modules\Base\Http\Requests\Access\User\CreateUserRequest;
use Modules\Base\Http\Requests\Access\User\StoreUserRequest;
use Modules\Base\Http\Requests\Access\User\EditUserRequest;
use Modules\Base\Http\Requests\Access\User\MarkUserRequest;
use Modules\Base\Http\Requests\Access\User\UpdateUserRequest;
use Modules\Base\Http\Requests\Access\User\DeleteUserRequest;
use Modules\Base\Http\Requests\Access\User\RestoreUserRequest;
use Modules\Base\Http\Requests\Access\User\ChangeUserPasswordRequest;
use Modules\Base\Http\Requests\Access\User\UpdateUserPasswordRequest;
use Modules\Base\Http\Requests\Access\User\PermanentlyDeleteUserRequest;
use Modules\Base\Services\UserServiceContract;

#use Modules\Addresses\Entities\Address;
#use Modules\Users\Entities\UserProfile;

/**
 * Class UserController
 */
class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $users;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var RoleRepository
     */
    protected $roles;

    /**
     * @var PermissionRepository
     */
    protected $permissions;

    protected $groups;

    /**
     * @param UserRepository                 $users
     * @param RoleRepository       $roles
     * @param PermissionRepository $permissions
     */
    public function __construct(
        UserRepository $users,
        RoleRepository $roles,
        PermissionRepository $permissions,
        PermissionGroupRepository $groups,
        UserServiceContract $userService
    )
    {
        $this->users        = $users;
        $this->roles        = $roles;
        $this->permissions  = $permissions;
        $this->groups       = $groups;
        $this->userService  = $userService;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        return view('access.index')
            ->withUsers($this->users->getUsersPaginated(config('access.users.default_per_page'), 1));
    }

    /**
     * @param  CreateUserRequest $request
     * @return mixed
     */
    public function create(CreateUserRequest $request)
    {
        return view('access.create')
            ->withRoles($this->roles->getAllRoles('sort', 'asc', true))
            ->withPermissions($this->permissions->getAllPermissions());
    }

    /**
     * @param  StoreUserRequest $request
     * @return mixed
     */
    public function store(StoreUserRequest $request)
    {
//        DB::beginTransaction();
//        try {
//            $user = $this->users->create(
//                $request->except(['assignees_roles', 'permission_user']),
//                $request->only('assignees_roles'),
//                $request->only('permission_user')
//            );
//
//            DB::commit();
//            Event::fire(new CreatedUserSuccessfully($user));
//        }
//        catch (Exception $e) {
//            DB::rollback();
//        }
//
        $this->userService->createUser($request->all());
		flash(trans('alerts.users.created'), 'success');
        return redirect()->route('admin.access.users.index');
    }

    /**
     * @param  $id
     * @param  EditUserRequest $request
     * @return mixed
     */
    public function edit($id, EditUserRequest $request)
    {
        $user = $this->users->findOrThrowException($id, true);
        return view('access.edit')
            ->withUser($user)
#            ->withAddress($user->addresses->first())
            ->withUserRoles($user->roles->pluck('id')->all())
            ->withRoles($this->roles->getAllRoles('sort', 'asc', true))
            ->withUserPermissions($user->permissions->pluck('id')->all())
            ->withPermissions($this->groups->getAllGroups());
    }

    /**
     * @param  $id
     * @param  UpdateUserRequest $request
     * @return mixed
     */
    public function update($id, UpdateUserRequest $request)
    {
//        DB::transaction(function () {
//            $user = $this->users->update($id,
//                $request->except(['assignees_roles', 'permission_user', 'profile', 'address']),
//                $request->only('assignees_roles'),
//                $request->only('permission_user')
//            );
//
////            if($user->profile) {
////                $user->profile->fill($request->only('profile')['profile'])->save();
////            }
////            else {
////                $user->profile()->create($request->only('profile')['profile']);
////            }
//
//            Event::fire(new UpdatedUserSuccessfully($user));
//        });
        $this->userService->updateUser($id, $request->all());
		flash(trans('alerts.users.updated'), 'success');
        return redirect()->route('admin.access.users.index');
    }

    /**
     * @param  $id
     * @param  DeleteUserRequest $request
     * @return mixed
     */
    public function destroy($id, DeleteUserRequest $request)
    {
        $this->users->destroy($id);
		flash(trans('alerts.users.deleted'), 'success');
        return redirect()->back();
    }

    /**
     * @param  $id
     * @param  PermanentlyDeleteUserRequest $request
     * @return mixed
     */
    public function delete($id, PermanentlyDeleteUserRequest $request)
    {
        $this->users->delete($id);
		flash(trans('alerts.users.deleted_permanently'), 'success');
        return redirect()->back();
    }

    /**
     * @param  $id
     * @param  RestoreUserRequest $request
     * @return mixed
     */
    public function restore($id, RestoreUserRequest $request)
    {
        $this->users->restore($id);
		flash(trans('alerts.users.restored'), 'success');
        return redirect()->back();
    }

    /**
     * @param  $id
     * @param  $status
     * @param  MarkUserRequest $request
     * @return mixed
     */
    public function mark($id, $status, MarkUserRequest $request)
    {
        $this->users->mark($id, $status);
		flash(trans('alerts.users.updated'), 'success');
        return redirect()->back();
    }

    /**
     * @return mixed
     */
    public function deactivated()
    {
        return view('access.deactivated')
            ->withUsers($this->users->getUsersPaginated(25, 0));
    }

    /**
     * @return mixed
     */
    public function deleted()
    {
        return view('access.deleted')
            ->withUsers($this->users->getDeletedUsersPaginated(25));
    }

    /**
     * @param  $id
     * @param  ChangeUserPasswordRequest $request
     * @return mixed
     */
    public function changePassword($id, ChangeUserPasswordRequest $request)
    {
        return view('access.change-password')
            ->withUser($this->users->findOrThrowException($id));
    }

    /**
     * @param  $id
     * @param  UpdateUserPasswordRequest $request
     * @return mixed
     */
    public function updatePassword($id, UpdateUserPasswordRequest $request)
    {
        $this->users->updatePassword($id, $request->all());
		flash(trans('alerts.users.updated_password'), 'success');
        return redirect()->route('admin.access.users.index');
    }

//    /**
//     * @param  $user_id
//     * @param  FrontendUserContract $user
//     * @param  ResendConfirmationEmailRequest $request
//     * @return mixed
//     */
//    public function resendConfirmationEmail($user_id, FrontendUserContract $user, ResendConfirmationEmailRequest $request)
//    {
//        $user->sendConfirmationEmail($user_id);
//		flash(trans('alerts.backend.users.confirmation_email'), 'success');
//        return redirect()->back();
//    }
}
