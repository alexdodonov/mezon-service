<?php

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
            \Mezon\Service\ServiceMockSecurityProvider::class,
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
            \Mezon\Service\ServiceMockSecurityProvider::class,
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
    public function testFetchActions(): void
    {
        // setup
        $service = new TestingBaseService(
            \Mezon\Service\ServiceBaseLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class);

        // test body
        $_GET['r'] = 'test';
        $service->run();

        // assertions
        $this->assertEquals('Action!', $service->getTransport()->result);
    }
}
