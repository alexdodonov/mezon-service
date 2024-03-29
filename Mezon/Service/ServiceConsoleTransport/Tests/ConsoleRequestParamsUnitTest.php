<?php
namespace Mezon\Service\ServiceConsoleTransport\Tests;

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
        $argv[0] = './vendor/phpunit/phpunit/phpunit';

        // test body
        $result = $requestParams->getParam('0');

        // assertions
        $this->assertEquals('./vendor/phpunit/phpunit/phpunit', $result);
    }

    /**
     * Testing getParam method
     */
    public function testGetParamDefault(): void
    {
        // setup
        $router = new Router();
        $requestParams = new ConsoleRequestParams($router);

        // test body
        $result = $requestParams->getParam('1000', 'default');

        // assertions
        $this->assertEquals('default', $result);
    }
}
