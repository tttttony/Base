<?php

namespace Modules\Base\Providers;

use Illuminate\Routing\Router;
use Modules\Base\Http\Middleware\Authenticate;
use Modules\Base\Services\Access\Access;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

/**
 * Class AccessServiceProvider
 * @package App\Providers
 */
class AccessServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Package boot method
     */
    public function boot(Router $router)
    {
        $this->registerBladeExtensions();

        $router->aliasMiddleware('auth', Authenticate::class);

        //TODO: Move this into a Auth Service Provider
        Passport::routes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAccess();
        $this->registerFacade();
        $this->registerBindings();
    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerAccess()
    {
        $this->app->bind('access', function ($app) {
            return new Access($app);
        });
    }

    /**
     * Register the vault facade without the user having to add it to the app.php file.
     *
     * @return void
     */
    public function registerFacade()
    {
        $this->app->booting(function () {
            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
            $loader->alias('Access', \Modules\Base\Services\Access\Facades\Access::class);
        });
    }

    /**
     * Register service provider bindings
     */
    public function registerBindings()
    {
        $this->app->bind(
            \Modules\Base\Repositories\UserRepository::class,
            \Modules\Users\Repositories\Eloquent\EloquentUserRepository::class
        );

        $this->app->bind(
            \Modules\Base\Repositories\UserRepository::class,
            \Modules\Base\Repositories\Eloquent\EloquentUserRepository::class
        );

        $this->app->bind(
            \Modules\Base\Repositories\RoleRepository::class,
            \Modules\Base\Repositories\Eloquent\EloquentRoleRepository::class
        );

        $this->app->bind(
            \Modules\Base\Repositories\PermissionRepository::class,
            \Modules\Base\Repositories\Eloquent\EloquentPermissionRepository::class
        );

        $this->app->bind(
            \Modules\Base\Repositories\PermissionGroupRepository::class,
            \Modules\Base\Repositories\Eloquent\EloquentPermissionGroupRepository::class
        );

        $this->app->bind(
            \Modules\Base\Repositories\PermissionDependencyRepository::class,
            \Modules\Base\Repositories\Eloquent\EloquentPermissionDependencyRepository::class
        );
    }

    /**
     * Register the blade extender to use new blade sections
     */
    protected function registerBladeExtensions()
    {
        Blade::directive('hello', function($expression){
            return "<?php echo 'Hello ' . {$expression}; ?>";
        });


        /**
         * Role based blade extensions
         * Accepts either string of Role Name or Role ID
         */
        Blade::directive('role', function ($role) {
            return "<?php if (access()->hasRole{$role}): ?>";
        });

        /**
         * Accepts array of names or id's
         */
        Blade::directive('roles', function ($roles) {
            return "<?php if (access()->hasRoles{$roles}): ?>";
        });

        Blade::directive('needsroles', function ($roles) {
            return '<?php if (access()->hasRoles(' . $roles . ', true)): ?>';
        });

        /**
         * Permission based blade extensions
         * Accepts wither string of Permission Name or Permission ID
         */

        Blade::directive('permission', function ($permission) {
            return "<?php if (access()->allow({$permission})): ?>";
        });

        /**
         * Accepts array of names or id's
         */
        Blade::directive('permissions', function ($permissions) {
            return "<?php if (access()->allowMultiple{$permissions}): ?>";
        });

        Blade::directive('needspermissions', function ($permissions) {
            return '<?php if (access()->allowMultiple(' . $permissions . ', true)): ?>';
        });

        /**
         * Generic if closer to not interfere with built in blade
         */
        Blade::directive('endauth', function () {
            return '<?php endif; ?>';
        });
    }
}