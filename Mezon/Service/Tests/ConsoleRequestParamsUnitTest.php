<?php
namespace Mezon\Service\Tests;

use Mezon\Router\Router;
use Mezon\Service\ServiceConsoleTransport\ConsoleRequestParams;

/**
 * 
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ConsoleRequestParamsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getParam method
     */
    public function testGetParam(): void
    {
        // setup
        $router = new Router();
        $requestParams = new ConsoleRequestParams($router);
        global $argv;
        $argv['param'] = 'value';

        // test body
        $result = $requestParams->getParam('param');

        // assertions
        $this->assertEquals('value', $result);
    }
}
