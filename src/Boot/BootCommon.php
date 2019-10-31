<?php


namespace Two\Boot;


use Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Two\Two;

abstract class BootCommon
{
    /**
     * Common boot operations. Initializes environment, config, event bus, services and factories.
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        $app->preBootCommon();

        $coreConfig = __DIR__ . '/../../config';
        $localConfig = $app->getConfigFolder();

        $env = Dotenv::create($app->getProjectRoot());
        $parameters = $env->load();
        $app->validateEnv($env);

        Two::loadConfig([$coreConfig, $localConfig], $parameters);

        Two::setBus($bus = new EventDispatcher());
        $app->registerEvents($bus);

        // load all .php files in the config/services/ folder.
        $serviceFiles = glob($localConfig . '/services/*.php');
        if ( $serviceFiles ) {
            foreach ( $serviceFiles as $file ) {
                if ( is_file($file) ) {
                    require_once $file;
                }
            }
        }

        $app->postBootCommon();
    }
}