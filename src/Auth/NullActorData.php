<?php


namespace Two\Auth;


class NullActorData extends ActorData
{
    public function isValid()
    {
        return false;
    }
}