<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForConsoleTransport extends ServiceLogic
{

    public function privateMethod(): string
    {
        return 'private';
    }

    public function publicMethod(): string
    {
        return 'public';
    }
}
