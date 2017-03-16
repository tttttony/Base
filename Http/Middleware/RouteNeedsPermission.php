<?php

namespace Modules\Base\Http\Middleware;

use Closure;

/**
 * Class RouteNeedsRole.
 */
class RouteNeedsPermission
{
    /**
     * @param $request
     * @param Closure $next
     * @param $permission
     * @param bool $needsAll
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $permission, $needsAll = false)
    {
        /*
         * Permission array
         */
        if (strpos($permission, ';') !== false) {
            $permissions = explode(';', $permission);
            $access = access()->allowMultiple($permissions, ($needsAll === 'true' ? true : false));
        } else {
            /**
             * Single permission.
             */
            $access = access()->allow($permission);
        }

        if (! $access) {
            flash('error', trans('auth.general_error'));
            return redirect('/'); //->route('frontend.index');
        }

        return $next($request);
    }
}
