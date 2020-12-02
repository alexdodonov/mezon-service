<?php
namespace Mezon\Service\Tests;

class ExceptionTestingBaseService extends TestingBaseService
{

    protected function initCustomRoutes(): void
    {
        // and here we emulate error
        throw (new \Exception("msg", 1));
    }
}
