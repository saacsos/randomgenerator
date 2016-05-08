<?php

namespace Saacsos\Randomgenerator\Facades;

use Illuminate\Support\Facades\Facade;

class RandomGenerator extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'randnerator';
    }
}