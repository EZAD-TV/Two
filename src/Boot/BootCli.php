<?php


namespace Two\Boot;


use Two\Two;

class BootCli extends BootCommon
{
    public function boot(Application $app)
    {
        parent::boot($app);

        $app->preBootCli();

        Two::setMode(Two::CLI);

        $app->postBootCli();
    }
}