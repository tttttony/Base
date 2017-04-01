<?php

namespace Modules\Base\Services\Access\Traits;

use Illuminate\Support\Facades\Auth;
use Modules\Base\Events\Auth\UserRegistered;
use Modules\Base\Http\Requests\Auth\RegisterRequest;
use Modules\Base\Services\UserService;

/**
 * Class RegistersUsers
 * @package App\Services\Access\Traits
 */
trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * @param RegisterRequest $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function register(RegisterRequest $request)
    {
        if (config('base.users.confirm_email')) {
            $user = $this->userService->registerUser($request->all());
            event(new UserRegistered($user));
            return redirect()->route('frontend.index')->withFlashSuccess(trans('exceptions.frontend.auth.confirmation.created_confirm'));
        } else {
            auth()->login($this->userService->registerUser($request->all()));
            event(new UserRegistered(access()->user()));
            return redirect($this->redirectPath());
        }
    }
}
