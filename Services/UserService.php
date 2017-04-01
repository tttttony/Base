<?php namespace Modules\Base\Services;

use DB;
use Event;
use Modules\Base\Events\User\CreatedUserSuccessfully;
use Modules\Base\Events\User\UpdatedUserSuccessfully;
use Modules\Base\Repositories\UserRepository;

class UserService implements UserServiceContract
{
    protected $user;

    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    public function registerUser(array $data, $provider = false)
    {
        DB::transaction(function () use ($data, $provider, &$user) {
            $user = $this->user->create($data, $provider);

            if (config('base.users.confirm_email') && $provider === false) {
                $this->sendConfirmationEmail($user);
            }
        });

        Event::fire(new CreatedUserSuccessfully($user));
        return $user;
    }

    public function updateUser($user_id, array $data)
    {
        DB::transaction(function () use ($user_id, $data, &$user) {
            $user = $this->user->update($user_id, $data);
        });

        Event::fire(new UpdatedUserSuccessfully($user));
        return $user;
    }

}
