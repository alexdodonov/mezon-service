<?php

class TestingServiceLogicForConsoleTransport extends \Mezon\Service\ServiceLogic
{

    public function privateMethod()
    {
        return 'private';
    }

    public function publicMethod()
    {
        return 'public';
    }
}

class ServiceConsoleTransportUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceConsoleTransport mocked object.
     */
    protected function getTransportMock(): object
    {
        return $this->getMockBuilder(\Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class)
            ->setMethods([
            'createSession'
        ])
            ->getMock();
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function getServiceLogicMock(): object
    {
        return $this->getMockBuilder(TestingServiceLogicForConsoleTransport::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'connect'
        ])
            ->getMock();
    }

    /**
     * Testing connect method.
     */
    public function testConstructor(): void
    {
        $transport = new \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport();

        $this->assertNotEquals(null, $transport->getSecurityProvider());
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault(): void
    {
        $transport = new \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport();
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString(): void
    {
        $transport = new \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport(
            \Mezon\Service\ServiceMockSecurityProvider::class);
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject(): void
    {
        $transport = new \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport(
            new \Mezon\Service\ServiceMockSecurityProvider());
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCall(): void
    {
        $mock = $this->getTransportMock();

        $serviceLogic = $this->getServiceLogicMock();

        $serviceLogic->expects($this->once())
            ->method('connect');

        $mock->callLogic($serviceLogic, 'connect');
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCallPublic(): void
    {
        $mock = $this->getTransportMock();

        $serviceLogic = $this->getServiceLogicMock();

        $serviceLogic->expects($this->once())
            ->method('connect');

        $mock->callPublicLogic($serviceLogic, 'connect');
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall(): void
    {
        // setup
        $_GET['r'] = '/public-method/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->expects($this->never())
            ->method('createSession');

        $mock->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        // test body and assertions
        $mock->run();
    }

    /**
     * Testing private call with createSession method.
     */
    public function testPrivateCall(): void
    {
        // setup
        $_GET['r'] = '/private-method/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->expects($this->once())
            ->method('createSession');

        $mock->addRoute('private-method', 'privateMethod', 'GET', 'private_call');

        // test body and assertions
        $mock->run();
    }

    /**
     * Testing 'run' method
     */
    public function testRun(): void
    {
        // setup
        $_GET['r'] = 'public-method';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->expects($this->never())
            ->method('createSession');

        $mock->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        // test body
        $mock->run();

        // assertions
        $this->assertEquals('public', $mock->result);
    }
}
