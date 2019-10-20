<?php

use Two\Factory;
use Two\Service;
use Two\Two;

function two_get($key, $name = '')
{
    return Service::get($key, $name);
}

function two_make($key, $name = '', $override = [])
{
    return Factory::get($key, $name, $override);
}

function two_url()
{
    return Two::url();
}