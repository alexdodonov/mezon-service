<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\ServiceModel;
use Mezon\Service\Service;

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

        $service = new Service(
            TestingLogic::class,
            ServiceModel::class,
            $this->getSecurityProvider(AS_STRING),
            $transport);
        $service->getTransport()->loadRoutesFromConfig(__DIR__ . '/conf/routes.php');
        $service->getTransport()->loadRoutes(json_decode(file_get_contents(__DIR__ . '/conf/routes.json'), true));

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
