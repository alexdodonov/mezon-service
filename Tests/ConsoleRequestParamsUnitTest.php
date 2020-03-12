<?php

class ConsoleRequestParamsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getParam method
     */
    public function testGetParam(): void
    {
        // setup
        $requestParams = new Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams();
        global $argv;
        $argv['param'] = 'value';

        // test body
        $result = $requestParams->getParam('param');

        // assertions
        $this->assertEquals('value', $result);
    }
}
