<?php
require_once (__DIR__ . '/TestService.php');

class ServiceUnitTest extends \Mezon\Service\Tests\ServiceUnitTests
{

    /**
     * Method tests does custom routes were loaded.
     * Trying to read routes both from php and json file and call routes from them.
     */
    public function testCustomRoutesLoading()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $service = new TestService(
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class,
            $this->getSecurityProvider(AS_STRING),
            TestLogic::class);

        // route from routes.php
        $_GET['r'] = 'test';
        $service->run();

        // route from routes.json
        $_GET['r'] = 'test2';
        $service->run();

        $this->expectException(Exception::class);
        $_GET['r'] = 'test3';
        $service->run();
    }
}
