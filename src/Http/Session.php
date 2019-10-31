<?php


namespace Two\Http;


use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\Session as BaseSession;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

class Session extends BaseSession
{
    public function __construct(SessionStorageInterface $storage = null,
                                AttributeBagInterface $attributes = null,
                                FlashBagInterface $flashes = null)
    {
        parent::__construct($storage, $attributes, $flashes);

        $authBag = new NamespacedAttributeBag('_two_auth');
        $authBag->setName('auth');
        $this->registerBag($authBag);
    }

    public function getAuthBag()
    {
        return $this->getBag('auth');
    }
}