<?php


namespace Two\Auth;

/**
 * An actor is someone or something performing an action. Use this class to get data on who or what is
 * doing the performing.
 *
 * Applications may wish to override this to add shortcuts like making $actor->customer()
 * call $actor->getData('customer').
 *
 * @package Two\Auth
 */
class Actor
{
    /**
     * @var ActorData[]
     */
    private $actorData = [];

    public function __construct()
    {
    }

    public function getData($key): ActorData
    {
        return $this->actorData[$key] ?? new NullActorData();
    }
}