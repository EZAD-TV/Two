<?php


namespace Two\Boot;


use Redis;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\RedisSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Two\Http\Input\Input;
use Two\Http\Session;
use Two\Service;
use Two\Two;

class BootHttp extends BootCommon
{
    /**
     * HTTP-related boot operations. Initializes request, input validator, sessions.
     *
     * @param Application $app
     */
    public function boot(Application $app)
    {
        parent::boot($app);

        $app->preBootHttp();

        Two::setMode(Two::HTTP);
        Two::setRequest($request = Request::createFromGlobals());
        Two::setInput(new Input($request));

        $this->setupSessions($app);

        $app->postBootHttp();
    }

    protected function setupSessions(Application $app)
    {
        if ( $app->shouldUseSessions(Two::request()) ) {
            $sessionStorage = Two::config('http.session.storage');
            if ( $sessionStorage === 'redis' ) {
                /** @var Redis $redis */
                $redis = Service::get(Two::config('http.session.redis.service'));
                $handler = new RedisSessionHandler($redis, ['prefix' => Two::config('http.session.redis.prefix')]);
            } else {
                $handler = new NativeFileSessionHandler(Two::config('http.session.fs.path'));
            }

            $storage = new NativeSessionStorage(Two::config('http.session.options'), $handler);
            $attributes = new NamespacedAttributeBag('_two', '.');
            $flashes = new FlashBag('_two_flashes');

            Two::setSession($session = new Session($storage, $attributes, $flashes));
            Two::request()->setSession($session);
        }
    }
}