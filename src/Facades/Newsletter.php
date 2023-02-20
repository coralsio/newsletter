<?php

namespace Corals\Modules\Newsletter\Facades;

use Illuminate\Support\Facades\Facade;

class Newsletter extends Facade
{
    /**
     * @return mixed
     */
    protected static function getFacadeAccessor()
    {
        return \Corals\Modules\Newsletter\Classes\Newsletter::class;
    }
}
