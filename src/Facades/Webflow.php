<?php

namespace Svikramjeet\Webflow\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Svikramjeet\Webflow\Webflow
 */
class Webflow extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Svikramjeet\Webflow\Webflow::class;
    }
}
