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

    public function ok()
    {
        return "ok";
    }

    public function privateMethod()
    {
        // nop
    }

    public function publicMethod()
    {
        // nop
    }

    public function methodException()
    {
        throw (new \Exception('Msg'));
    }

    public function methodRestException()
    {
        throw (new \Mezon\Rest\Exception('Msg', 0, 1, 1));
    }
}
