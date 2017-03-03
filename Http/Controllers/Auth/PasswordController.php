<?php namespace Modules\Base\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Modules\Base\Services\Access\Traits\ChangePasswords;
use Modules\Base\Services\Access\Traits\ResetsPasswords;
use Modules\Base\Repositories\UserRepository;

/**
 * Class PasswordController
 * @package Modules\Base\Http\Controllers\Auth
 */
class PasswordController extends Controller
{

    use ChangePasswords, ResetsPasswords;

    /**
     * Where to redirect the user after their password has been successfully reset
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }
}