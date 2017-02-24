<?php namespace Modules\Base\Repositories\Eloquent;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

use Modules\Base\Exceptions\GeneralException;

use Modules\Base\Entities\User\User;
use Modules\Base\Entities\User\SocialLogin;

use Modules\Base\Repositories\RoleRepository;
use Modules\Base\Repositories\UserRepository;

/**
 * Class EloquentUserRepository
 * @package App\Repositories\Frontend\User
 */
abstract class EloquentUserRepository implements UserRepository
{

    /**
     * @var RoleRepository
     */
    protected $role;

    /**
     * @var UserRepository
     */
    protected $user;

    /**
     * @param RoleRepositoryContract $role
     */
    public function __construct(RoleRepository $role, User $user)
    {
        $this->role = $role;
        $this->user = $user;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->user->findOrFail($id);
    }

    /**
     * @param $email
     * @return bool
     */
    public function findByEmail($email) {
        $user = $this->user->where('email', $email)->first();

        if (! $user instanceof User)
            throw new GeneralException(trans('exceptions.frontend.auth.confirmation.not_found'));

        return $user;
    }

    /**
     * @param $token
     * @return mixed
     * @throws GeneralException
     */
    public function findByToken($token) {
        $user = $this->user->where('confirmation_code', $token)->first();

        if (! $user instanceof User)
            throw new GeneralException(trans('exceptions.frontend.auth.confirmation.not_found'));

        return $user;
    }

    /**
     * @param array $data
     * @param bool $provider
     * @return static
     */
    public function create(array $data, $provider = false)
    {
        /*
         * needs a db transaction
         */
        if ($provider) {
            $user = $this->user->create([
                'username' => $data['email'],
                'email' => $data['email'],
                'password' => null,
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => 1,
                'status' => 1,
            ]);
        } else {
            $user =$this->user->create([
                'username' => $data['email'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'confirmation_code' => md5(uniqid(mt_rand(), true)),
                'confirmed' => config('access.users.confirm_email') ? 0 : 1,
                'status' => 1,
            ]);
        }

        /**
         * Add the default site role to the new user
         */
        $user->attachRole($this->role->getDefaultUserRole());

        /**
         * Return the user object
         */
        return $user;
    }

    /**
     * @param $data
     * @param $provider
     * @return EloquentUserRepository
     */
    public function findOrCreateSocial($data, $provider)
    {
        /**
         * Check to see if there is a user with this email first
         */
        $user = $this->findByEmail($data->email);

        /**
         * If the user does not exist create them
         * The true flag indicate that it is a social account
         * Which triggers the script to use some default values in the create method
         */
        if (! $user) {
            $user = $this->create([
                'name'  => $data->name,
                'email' => $data->email,
            ], true);
        }

        /**
         * See if the user has logged in with this social account before
         */
        if (! $user->hasProvider($provider)) {
            /**
             * Gather the provider data for saving and associate it with the user
             */
            $user->providers()->save(new SocialLogin([
                'provider'    => $provider,
                'provider_id' => $data->id,
            ]));
        }

        /**
         * Return the user object
         */
        return $user;
    }

    /**
     * @param $token
     * @return bool
     * @throws GeneralException
     */
    public function confirmAccount($token)
    {
        $user = $this->findByToken($token);

        if ($user->confirmed == 1) {
            throw new GeneralException(trans('exceptions.frontend.auth.confirmation.already_confirmed'));
        }

        if ($user->confirmation_code == $token) {
            $user->confirmed = 1;
            return $user->save();
        }

        throw new GeneralException(trans('exceptions.frontend.auth.confirmation.mismatch'));
    }

    /**
     * @param $user
     * @return mixed
     */
    public function sendConfirmationEmail($user)
    {
        //$user can be user instance or id
        if (! $user instanceof User) {
            $user = $this->find($user);
        }

        return Mail::send('auth.emails.confirm', ['token' => $user->confirmation_code], function ($message) use ($user) {
            $message->to($user->email, $user->name)->subject(app_name() . ': ' . trans('exceptions.frontend.auth.confirmation.confirm'));
        });
    }

    /**
     * @param $token
     * @return mixed
     * @throws GeneralException
     */
    public function resendConfirmationEmail($token) {
        return $this->sendConfirmationEmail($this->findByToken($token));
    }

    /**
     * @param $id
     * @param $input
     * @return mixed
     * @throws GeneralException
     */
    public function update($id, $input)
    {
        $user = $this->find($id);

        if ($user->canChangeEmail()) {
            //Address is not current address
            if ($user->email != $input['email']) {
                //Emails have to be unique
                if ($this->findByEmail($input['email'])) {
                    throw new GeneralException(trans('exceptions.frontend.auth.email_taken'));
                }

                $user->email = $input['email'];
            }
        }

        $user->save();

        return $user;
    }

    /**
     * @param $input
     * @return mixed
     * @throws GeneralException
     */
    public function changePassword($input)
    {
        $user = $this->find(access()->id());

        if (Hash::check($input['old_password'], $user->password)) {
            $user->password = bcrypt($input['password']);
            return $user->save();
        }

        throw new GeneralException(trans('exceptions.frontend.auth.password.change_mismatch'));
    }

}