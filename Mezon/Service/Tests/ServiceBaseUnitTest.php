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

class ServiceBaseUnitTest extends TestCase
{

    /**
     * Testing getTransport method
     */
    public function testGetTransport()
    {
        // setup
        $service = new ServiceBase(
            ServiceBaseLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceHttpTransport::class);

        // test body and assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());
    }

    /**
     * Testing getTransport method
     */
    public function testSetTransport()
    {
        // setup
        $service = new ServiceBase(
            ServiceBaseLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceHttpTransport::class);

        // assertions
        $this->assertInstanceOf(ServiceHttpTransport::class, $service->getTransport());

        // test body
        $service->setTransport(new ServiceRestTransport());

        // assertions
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsGet(): void
    {
        // setup
        $service = new TestingBaseService(
            ServiceBaseLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceConsoleTransport::class);

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
        $service = new TestingBaseService(
            ServiceBaseLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceConsoleTransport::class);

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
        new ExceptionTestingBaseService(
            ServiceBaseLogic::class,
            ServiceModel::class,
            MockProvider::class,
            TestingTransport::class);
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"message"', $content);
        $this->assertStringContainsString('"code"', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
