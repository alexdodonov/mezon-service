<?php

use Mezon\Security\MockProvider;

class TestingBaseService extends \Mezon\Service\ServiceBase implements \Mezon\Service\ServiceBaseLogicInterface
{

    public function actionTest(): string
    {
        return 'Action!';
    }

    protected function initCustomRoutes(): void
    {
        // we don't need to load custom routes
    }
}

class ExceptionTestingBaseService extends TestingBaseService
{

    protected function initCustomRoutes(): void
    {
        // and here we emulate error
        throw (new \Exception("msg", 1));
    }
}

class TestingTransport extends \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport
{

    protected function die(): void
    {}
}

class ServiceBaseUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getTransport method
     */
    public function testGetTransport()
    {
        // setup
        $service = new \Mezon\Service\ServiceBase(
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            MockProvider::class,
            \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport::class);

        // test body and assertions
        $this->assertInstanceOf(
            \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport::class,
            $service->getTransport());
    }

    /**
     * Testing getTransport method
     */
    public function testSetTransport()
    {
        // setup
        $service = new \Mezon\Service\ServiceBase(
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            MockProvider::class,
            \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport::class);

        // assertions
        $this->assertInstanceOf(
            \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport::class,
            $service->getTransport());

        // test body
        $service->setTransport(new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());

        // assertions
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());
    }

    /**
     * Testing fetchActions call
     */
    public function testFetchActionsGet(): void
    {
        // setup
        $service = new TestingBaseService(
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            MockProvider::class,
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class);

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
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            MockProvider::class,
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class);

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
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            MockProvider::class,
            \TestingTransport::class);
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"message"', $content);
        $this->assertStringContainsString('"code"', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
