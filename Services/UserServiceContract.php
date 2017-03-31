<?php namespace Modules\Base\Services;

/**
 * Interface UserServiceContract
 * @package Modules\Base\Services\UserServiceContract
 */
interface UserServiceContract
{
    public function registerUser(array $data, $provider = false);
    public function updateUser($user_id, array $data);
}