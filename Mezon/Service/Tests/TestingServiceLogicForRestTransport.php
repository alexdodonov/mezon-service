<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceLogic;

class TestingServiceLogicForRestTransport extends ServiceLogic
{

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
