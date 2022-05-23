<?php
namespace Mezon\Service\ServiceConsoleTransport\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForConsoleTransport extends ServiceLogic
{

    // TODO use service-transport/FakeServiceLogic class instead of TestingServiceLogicForConsoleTransport
    public function privateMethod(): string
    {
        return 'private';
    }

    public function publicMethod(): string
    {
        return 'public';
    }
}
