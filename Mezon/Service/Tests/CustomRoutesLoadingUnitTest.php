<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\ServiceModel;
use Mezon\Service\Service;
use Mezon\Security\MockProvider;
use Mezon\Conf\Conf;
use PHPUnit\Framework\TestCase;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CustomRoutesLoadingUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        // TODO move to the base class
        Conf::setConfigStringValue('system/layer', 'mock');
    }

    /**
     * Method tests does custom routes were loaded.
     * Trying to read routes both from php and json file and call routes from them.
     */
    public function testCustomRoutesLoading(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $transport = new ServiceConsoleTransport();

        $logic = new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel());

        $transport->setServiceLogic($logic);
        $transport->loadRoutesFromConfig(__DIR__ . '/conf/routes.php');
        $transport->loadRoutes(json_decode(file_get_contents(__DIR__ . '/conf/routes.json'), true));

        $service = new Service($transport);

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
