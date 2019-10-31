<?php


namespace Two\Boot;


use Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;

class Application
{
    public function shouldUseSessions(Request $request)
    {
        return true;
    }

    public function getProjectRoot()
    {
        // Boot, src, two, ezadtv, vendor, project
        return __DIR__ . '/../../../../..';
    }

    public function getConfigFolder()
    {
        return $this->getProjectRoot() . '/config';
    }

    /**
     * Allows validating parameters in .env to make sure values are not missing and are the proper data types.
     * The application will fail to launch if validation fails.
     *
     * @param Dotenv $env
     */
    public function validateEnv(Dotenv $env)
    {

    }

    public function registerEvents(EventDispatcherInterface $dispatcher)
    {

    }

    public function preBootCommon()
    {

    }

    public function postBootCommon()
    {

    }

    public function preBootHttp()
    {

    }

    public function postBootHttp()
    {

    }

    public function preBootCli()
    {

    }

    public function postBootCli()
    {

    }
}