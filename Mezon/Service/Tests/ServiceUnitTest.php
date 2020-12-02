<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\ServiceModel;

class ServiceUnitTest extends ServiceUnitTests
{

    /**
     * Method tests does custom routes were loaded.
     * Trying to read routes both from php and json file and call routes from them.
     */
    public function testCustomRoutesLoading()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $transport = $this->getMockBuilder(ServiceConsoleTransport::class)
            ->setMethods([
            'die'
        ])
            ->getMock();

        $service = new TestingService(
            TestingLogic::class,
            ServiceModel::class,
            $this->getSecurityProvider(AS_STRING),
            $transport);

        // route from routes.php
        $_GET['r'] = 'test';
        $service->run();

        // route from routes.json
        $_GET['r'] = 'test2';
        $service->run();

        $_GET['r'] = 'test3';
        ob_start();
        $service->run();
        $content = ob_get_contents();
        ob_end_clean();
        $this->assertStringContainsString('"message"', $content);
    }
}
