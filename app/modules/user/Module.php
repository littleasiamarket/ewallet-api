<?php
namespace Softco\User;

use \Phalcon\Loader;
use \Phalcon\Mvc\View;
use \Phalcon\DiInterface;
use \Phalcon\Mvc\Dispatcher;
use \Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

    public function registerServices(\Phalcon\DiInterface $di)
    {
        $this->registerServicesAdapt($di);
    }

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = NULL)
    {
        $loader = new Loader();

        $loader->registerNamespaces([
            'Softco\User\Controllers' => __DIR__ . '/controllers/'
        ]);

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param \Phalcon\DI $di
     */
    public function registerServicesAdapt(\Phalcon\DiInterface $di)
    {
        /**
         * Read configuration
         */
        $config = include __DIR__ . "/config/config.php";

        /**
         * Registering config
         */
        $di['config'] = function () use ($config) {
            return $config;
        };

        $dispatcher = $di->get('dispatcher');
        // Registering a dispatcher
        $di->set('dispatcher', function () use ($dispatcher) {

            $dispatcher->setDefaultNamespace('Softco\User\Controllers');

            return $dispatcher;
        });

        $view = $di->get('view');
        /**
         * Setting up the view component
         */
        $di['view'] = function () use ($view, $config) {
            return $view;
        };
    }
}
