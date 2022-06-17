<?php
namespace Mezon\Service\Tests\Mocks;

class ExceptionTestingBaseService extends TestingBaseService
{

    protected function initCustomRoutes(): void
    {
        // and here we emulate error
        throw (new \Exception("msg", 1));
    }
}
