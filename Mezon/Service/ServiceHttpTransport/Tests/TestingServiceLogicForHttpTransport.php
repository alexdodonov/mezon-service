<?php
namespace Mezon\Service\ServiceHttpTransport\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForHttpTransport extends ServiceLogic
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
