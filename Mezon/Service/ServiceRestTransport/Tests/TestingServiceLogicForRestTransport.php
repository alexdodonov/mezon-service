<?php
namespace Mezon\Service\ServiceRestTransport\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForRestTransport extends ServiceLogic
{

    // TODO use service-transport/FakeServiceLogic as base class, it will let at leas remove constructor, privateMethod, publicMethod
    // or remove one usage of this class and replace it with FakeServiceLogic in unit-tests
    
    /**
     * Constructor
     */
    public function __construct()
    {
        // nop
    }

    public function ok(): string
    {
        return "ok";
    }

    public function privateMethod(): void
    {
        // nop
    }

    public function publicMethod(): void
    {
        // nop
    }

    public function methodException(): void
    {
        throw (new \Exception('Msg'));
    }

    public function methodRestException(): void
    {
        throw (new \Mezon\Rest\Exception('Msg', 0, 1, '1'));
    }
}
