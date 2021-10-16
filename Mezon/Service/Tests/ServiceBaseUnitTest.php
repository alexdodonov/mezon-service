<?php
namespace Mezon\Service\Tests;

use Mezon\Security\MockProvider;
use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceBase;
use Mezon\Service\ServiceBaseLogic;
use Mezon\Service\ServiceModel;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\Tests\Mocks\TestingTransport;
use Mezon\Transport\Tests\MockParamsFetcher;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceBaseUnitTest extends TestCase
{

    /**
     * Testing getTransport method
     */
    public function testGetTransport()
    {
        // setup
        $provider = new MockProvider();
        $service = new ServiceBase(new ServiceHttpTransport($provider));

        // test body and assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());
    }

    /**
     * Testing getTransport method
     */
    public function testSetTransport()
    {
        // setup
        $provider = new MockProvider();
        $service = new ServiceBase(new ServiceHttpTransport($provider));

        // assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());

        // test body
        $service->setTransport(new ServiceRestTransport($provider));

        // assertions
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsGet(): void
    {
        // setup
        $provider = new MockProvider();
        $service = new TestingBaseService(new ServiceConsoleTransport($provider));

        // test body
        $_GET['r'] = 'test';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $service->run();

        // assertions
        $this->assertEquals('Action!', $service->getTransport()->result);
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsPost(): void
    {
        // setup
        $provider = new MockProvider();
        $service = new TestingBaseService(new ServiceConsoleTransport($provider));

        // test body
        $_GET['r'] = 'test';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $service->run();

        // assertions
        $this->assertEquals('Action!', $service->getTransport()->result);
    }

    /**
     * Testing exception handling while constructor call
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and assertions
        ob_start();
        $provider = new MockProvider();
        new ExceptionTestingBaseService(new TestingTransport($provider));
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"message"', $content);
        $this->assertStringContainsString('"code"', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
