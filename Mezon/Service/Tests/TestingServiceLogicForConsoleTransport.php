<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForConsoleTransport extends ServiceLogic
{

    public function privateMethod()
    {
        return 'private';
    }

    public function publicMethod()
    {
        return 'public';
    }
}
