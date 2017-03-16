<?php namespace Modules\Base\Http\Controllers\Auth;

use Modules\Base\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;

use Modules\Base\Services\UserService;

use Modules\Base\Services\Access\Traits\AuthenticatesAndRegistersUsers;
use Modules\Base\Services\Access\Traits\ConfirmUsers;
use Modules\Base\Services\Access\Traits\UseSocialite;
use Modules\Base\Repositories\UserRepository;

/**
 * Class AuthController
 * @package App\Http\Controllers\Frontend\Auth
 */
class AuthController extends Controller
{
    use AuthenticatesAndRegistersUsers, ConfirmUsers, ThrottlesLogins, UseSocialite;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Where to redirect users after they logout
     *
     * @var string
     */
    protected $redirectAfterLogout = '/';

    /**
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user, UserService $userService)
    {
        $this->user = $user;
        $this->userService = $userService;
    }
}
