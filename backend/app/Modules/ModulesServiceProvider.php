<?php

namespace App\Modules;

use Illuminate\Routing\Router;

/**
 * ServiceProvider
 *
 * The service provider for the modules. After being registered
 * it will make sure that each of the modules are properly loaded
 * i.e. with their routes, views etc.
 * https://kamranahmed.info/blog/2015/12/03/creating-a-modular-application-in-laravel/
 *
 * @author Kamran Ahmed <kamranahmed.se@gmail.com>
 * @package App\Modules
 */
class ModulesServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Modules';

    protected $apiPrefix = 'api';

//    protected $middleware = ['cors'];

    /**
     * Will make sure that the required modules have been fully loaded
     * @param Router $router
     * @return void
     */
    public function boot(Router $router)
    {
        $router->group([
            'prefix' => $this->apiPrefix,
            'namespace' => $this->namespace,
//            'middleware' => $this->middleware,
        ], function () {
            require __DIR__ . '/Home/routes.php';
            require __DIR__ . '/User/routes.php';
            require __DIR__ . '/Auth/routes.php';
            require __DIR__ . '/Location/routes.php';
            require __DIR__ . '/AdministrativeArea/routes.php';
            require __DIR__ . '/Crawler/routes.php';
            require __DIR__ . '/Translation/routes.php';
//            $modules = config("module.modules");
//
//            // For each of the registered modules, include their routes
//            foreach ($modules as $module) {
//                // Load the routes for each of the modules
//                if (file_exists(__DIR__ . '/' . $module . '/routes.php')) {
//                    require __DIR__ . '/' . $module . '/routes.php';
//                }
//            }
        });
    }

    public function register() {}
}
