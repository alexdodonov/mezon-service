<?php

class FakeSecurityProviderForHttpTransport
{
}

class TestingServiceLogicForHttpTransport extends \Mezon\Service\ServiceLogic
{

    public function privateMethod()
    {}

    public function publicMethod()
    {}
}

class ServiceHttpTransportUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function getServiceLogicMock()
    {
        return $this->getMockBuilder(TestingServiceLogicForHttpTransport::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'connect'
        ])
            ->getMock();
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceRestTransport mocked object.
     */
    protected function getTransportMock()
    {
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceHttpTransport\ServiceHttpTransport::class)
            ->setMethods([
            'header',
            'createSession'
        ])
            ->getMock();

        $mock->expects($this->once())
            ->method('header');

        $mock->paramsFetcher = $this->getMockBuilder(\Mezon\Service\ServiceHttpTransport\HttpRequestParams::class)
            ->setMethods([
            'getSessionIdFromHeaders'
        ])
            ->disableOriginalConstructor()
            ->getMock();

        $mock->paramsFetcher->method('getSessionIdFromHeaders')->willReturn('token');

        return $mock;
    }

    /**
     * Testing connect method.
     */
    public function testConstructor()
    {
        new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport();

        $this->addToAssertionCount(1);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault()
    {
        $transport = new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport();
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $transport->securityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString()
    {
        $transport = new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport(
            FakeSecurityProviderForHttpTransport::class);
        $this->assertInstanceOf(FakeSecurityProviderForHttpTransport::class, $transport->securityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject()
    {
        $transport = new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport(
            new FakeSecurityProviderForHttpTransport());
        $this->assertInstanceOf(FakeSecurityProviderForHttpTransport::class, $transport->securityProvider);
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCall()
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
    public function testSingleHeaderCallPublic()
    {
        $mock = $this->getTransportMock();

        $serviceLogic = $this->getServiceLogicMock();

        $serviceLogic->expects($this->once())
            ->method('connect');

        $mock->callPublicLogic($serviceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValues()
    {
        $mock = $this->getTransportMock();

        $mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callLogic($serviceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublic()
    {
        $mock = $this->getTransportMock();

        $mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callPublicLogic($serviceLogic, 'connect');
    }

    /**
     * Getting tricky mock object.
     */
    protected function getMockEx(string $mode)
    {
        $mock = $this->getTransportMock();

        $mock->serviceLogic = $this->getServiceLogicMock();

        $mock->method('header')->with($this->equalTo('Content-type'), $this->equalTo('text/html; charset=utf-8'));

        $mock->addRoute('connect', 'connect', 'GET', $mode, [
            'content_type' => 'text/html; charset=utf-8'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = 'connect';

        return $mock;
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesEx()
    {
        $mock = $this->getMockEx('callLogic');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublicEx()
    {
        $mock = $this->getMockEx('publicCall');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall()
    {
        $mock = $this->getTransportMock();

        $mock->serviceLogic = $this->getServiceLogicMock();

        $mock->expects($this->never())
            ->method('createSession');

        $mock->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        $mock->getRouter()->callRoute('/public-method/');
    }

    /**
     * Testing private call with createSession method.
     */
    public function testPrivateCallNoException()
    {
        $mock = $this->getTransportMock();

        $mock->serviceLogic = $this->getServiceLogicMock();

        $mock->expects($this->once())
            ->method('createSession');

        $mock->addRoute('private-method', 'privateMethod', 'GET', 'private_call');

        $mock->getRouter()->callRoute('/private-method/');
    }
}
