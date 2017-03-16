<?php namespace Modules\Base\Services;

use DB;
use Modules\Base\Repositories\UserRepository;

class UserService
{
    protected $user;

    public function __construct(
        UserRepository $user
    )
    {
        $this->user = $user;
    }

    public function registerUser(array $data, $provider = false) {
        DB::beginTransaction();

        try {
            $user = $this->user->create($data, $provider);

            /**
             * If users have to confirm their email and this is not a social account,
             * send the confirmation email
             *
             * If this is a social account they are confirmed through the social provider by default
             */
            if (config('access.users.confirm_email') && $provider === false) {
                $this->sendConfirmationEmail($user);
            }

            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }

        return $user;
    }

    public function updateUser($user_id, array $data)
    {
        DB::beginTransaction();

        try {
            $user = $this->user->update($user_id, $data);
            DB::commit();
        }
        catch(\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

}
