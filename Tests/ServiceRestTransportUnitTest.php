<?php

class FakeSecurityProviderForRestTransport
{
}

class TestingServiceLogicForRestTransport extends \Mezon\Service\ServiceLogic
{

    public function privateMethod()
    {}

    public function publicMethod()
    {}

    public function methodException()
    {
        throw (new \Exception('Msg'));
    }

    public function methodRestException()
    {
        throw (new \Mezon\Service\ServiceRestTransport\RestException('Msg', 0, 1, 1));
    }
}

class ServiceRestTransportTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceRestTransport mocked object.
     */
    protected function getTransportMock()
    {
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceRestTransport\ServiceRestTransport::class)
            ->setMethods([
            'header',
            'createSession',
            'errorResponse',
            'parentErrorResponse'
        ])
            ->getMock();

        $mock->expects($this->once())
            ->method('header');
        $mock->method('errorResponse')->willThrowException(
            new \Mezon\Service\ServiceRestTransport\RestException('Msg', 0, 1, 1));
        $mock->method('parentErrorResponse')->willThrowException(new \Exception('Msg', 0));

        return $mock;
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object.
     */
    protected function getServiceLogicMock()
    {
        return $this->getMockBuilder(TestingServiceLogicForRestTransport::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'connect'
        ])
            ->getMock();
    }

    /**
     * Testing connect method.
     */
    public function testConstructor()
    {
        $transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport();

        $this->assertNotEquals(null, $transport->securityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault()
    {
        $transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport();
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $transport->securityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString()
    {
        $transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(
            FakeSecurityProviderForRestTransport::class);
        $this->assertInstanceOf(FakeSecurityProviderForRestTransport::class, $transport->securityProvider);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject()
    {
        $transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(
            new FakeSecurityProviderForRestTransport());
        $this->assertInstanceOf(FakeSecurityProviderForRestTransport::class, $transport->securityProvider);
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
     * Setup method call
     *
     * @param string $methodName
     *            Method name
     * @return object Mock object
     */
    protected function setupMethod(string $methodName): object
    {
        $mock = $this->getTransportMock();

        $mock->serviceLogic = $this->getServiceLogicMock();

        $mock->expects($this->never())
            ->method('createSession');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mock->addRoute('public-method', $methodName, 'GET', 'public_call');

        return $mock;
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall()
    {
        // setup
        $mock = $this->setupMethod('publicMethod');

        // test body and assertions
        $mock->getRouter()->callRoute('/public-method/');
    }

    /**
     * Setup method call
     *
     * @param string $methodName
     *            Method name
     * @return object Mock object
     */
    protected function setupPrivateMethod(string $methodName): object
    {
        $mock = $this->getTransportMock();

        $mock->serviceLogic = $this->getServiceLogicMock();

        $mock->expects($this->once())
            ->method('createSession');

        $mock->addRoute('private-method', $methodName, 'GET', 'private_call');

        return $mock;
    }

    /**
     * Testing private call with createSession method.
     */
    public function testPrivateCall()
    {
        // setup
        $mock = $this->setupPrivateMethod('privateMethod');

        // test body and assertions
        $mock->getRouter()->callRoute('/private-method/');
    }

    /**
     * Testing public call with exception throwing
     */
    public function testPublicCallException()
    {
        // setup
        $mock = $this->setupMethod('methodException');

        $this->expectException(Exception::class);

        // test body and assertions
        $mock->getRouter()->callRoute('/public-method/');
    }

    /**
     * Testing public call with exception throwing
     */
    public function testPublicCallRestException()
    {
        // setup
        $mock = $this->setupMethod('methodRestException');

        $this->expectException(Exception::class);
        // test body and assertions
        $mock->getRouter()->callRoute('/public-method/');
    }

    /**
     * Testing private call with exception throwing
     */
    public function testPrivateCallException()
    {
        // setup
        $mock = $this->setupPrivateMethod('methodException');

        $this->expectException(Exception::class);

        // test body and assertions
        $mock->getRouter()->callRoute('/private-method/');
    }

    /**
     * Testing private call with exception throwing
     */
    public function testPrivateCallRestException()
    {
        // setup
        $mock = $this->setupPrivateMethod('methodRestException');

        $this->expectException(Exception::class);

        // test body and assertions
        $mock->getRouter()->callRoute('/private-method/');
    }

    /**
     * Testing errorResponse method
     */
    public function testErrorResponseException(): void
    {
        // setup
        $e = new Exception('msg', 1);
        $Transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport();

        // test body
        $result = $Transport->errorResponse($e);

        // assertions
        $this->assertEquals('msg', $result['message']);
        $this->assertEquals(1, $result['code']);
        $this->assertEquals('service', $result['service']);
    }

    /**
     * Testing errorResponse method
     */
    public function testErrorResponseRestException(): void
    {
        // setup
        $e = new \Mezon\Service\ServiceRestTransport\RestException('msg', 1,200, 'body');
        $Transport = new \Mezon\Service\ServiceRestTransport\ServiceRestTransport();

        // test body
        $result = $Transport->errorResponse($e);

        // assertions
        $this->assertEquals('msg', $result['message']);
        $this->assertEquals(1, $result['code']);
        $this->assertEquals('service', $result['service']);
        $this->assertEquals(200, $result['http_code']);
        $this->assertEquals('body', $result['http_body']);
    }
}
