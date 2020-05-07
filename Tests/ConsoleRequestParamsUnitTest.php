<?php

class ConsoleRequestParamsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getParam method
     */
    public function testGetParam(): void
    {
        // setup
        $router = new \Mezon\Router\Router();
        $requestParams = new Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams($router);
        global $argv;
        $argv['param'] = 'value';

        // test body
        $result = $requestParams->getParam('param');

        // assertions
        $this->assertEquals('value', $result);
    }
}
