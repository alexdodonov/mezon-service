<?php
namespace Mezon\Service\Tests;

use Mezon\Security\MockProvider;
use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Conf\Conf;
use Mezon\Service\ServiceModel;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class FetchActionsUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        Conf::setConfigStringValue('system/layer', 'mock');
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsGet(): void
    {
        // setup
        $transport = new ServiceConsoleTransport();
        $transport->setServiceLogic(
            new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel()));
        $service = new TestingBaseService($transport);

        // test body
        $_GET['r'] = 'test3';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $service->run();

        // assertions
        $this->assertEquals('Action!', ServiceConsoleTransport::$result);
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsPost(): void
    {
        // setup
        $transport = new ServiceConsoleTransport();
        $transport->setServiceLogic(
            new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel()));
        $service = new TestingBaseService($transport);

        // test body
        $_GET['r'] = 'test3';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $service->run();

        // assertions
        $this->assertEquals('Action!', ServiceConsoleTransport::$result);
    }

    /**
     * Testing exception handling while constructor call
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and assertions
        ob_start();
        new ExceptionTestingBaseService(new ServiceConsoleTransport());
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"message"', $content);
        $this->assertStringContainsString('"code"', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
