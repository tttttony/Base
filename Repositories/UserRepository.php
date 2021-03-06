<?php namespace Modules\Base\Repositories;

/**
 * Interface UserContract
 * @package App\Repositories\Frontend\User
 */
interface UserRepository
{
    /**
     * @param $id
     * @return mixed
     */
    public function find($id);

    /**
     * @param $email
     * @return mixed
     */
    public function findByEmail($email);

    /**
     * @param $token
     * @return mixed
     */
    public function findByToken($token);

    /**
     * @param array $data
     * @param bool $provider
     * @return mixed
     */
    public function create($data, $provider = false);

    /**
     * @param $data
     * @param $provider
     * @return mixed
     */
    public function findOrCreateSocial($data, $provider);

    /**
     * @param $token
     * @return mixed
     */
    public function confirmAccount($token);

    /**
     * @param $user
     * @return mixed
     */
    public function sendConfirmationEmail($user);

    /**
     * @param $id
     * @param $input
     * @return mixed
     */
    public function update($id, $input);

    /**
     * @param  $input
     * @return mixed
     */
    public function changePassword($input);
}
