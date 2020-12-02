<?php
namespace Mezon\Service\Tests;

use Mezon\Service\Service;

class ExceptionTestingService extends Service
{

    protected function initCustomRoutes(): void
    {
        // not loading routes from config
    }

    protected function initCommonRoutes(): void
    {
        // and here we emulate error
        throw (new \Exception("msg", 1));
    }
}
