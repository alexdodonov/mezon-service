<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Transport\HttpRequestParams;
use Mezon\Security\MockProvider;

class ServiceHttpTransportUnitTest extends TestCase
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
        $mock = $this->getMockBuilder(ServiceHttpTransport::class)
            ->setMethods([
            'header',
            'createSession'
        ])
            ->getMock();

        $mock->expects($this->once())
            ->method('header');

        $mock->setParamsFetcher(
            $this->getMockBuilder(HttpRequestParams::class)
                ->setMethods([
                'getSessionIdFromHeaders'
            ])
                ->disableOriginalConstructor()
                ->getMock());

        $mock->getParamsFetcher()
            ->method('getSessionIdFromHeaders')
            ->willReturn('token');

        return $mock;
    }

    /**
     * Testing connect method.
     */
    public function testConstructor()
    {
        new ServiceHttpTransport();

        $this->addToAssertionCount(1);
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitDefault()
    {
        $transport = new ServiceHttpTransport();
        $this->assertInstanceOf(MockProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitString()
    {
        $transport = new ServiceHttpTransport(MockProvider::class);
        $this->assertInstanceOf(MockProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that security provider was set.
     */
    public function testSecurityProviderInitObject()
    {
        $transport = new ServiceHttpTransport(new MockProvider());
        $this->assertInstanceOf(MockProvider::class, $transport->getSecurityProvider());
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

        $mock->method('header')->with($this->equalTo('Content-Type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callLogic($serviceLogic, 'connect');
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublic()
    {
        $mock = $this->getTransportMock();

        $mock->method('header')->with($this->equalTo('Content-Type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callPublicLogic($serviceLogic, 'connect');
    }

    /**
     * Getting tricky mock object
     */
    protected function getTransportMockEx(string $mode = 'publicCall')
    {
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->method('header')->with($this->equalTo('Content-Type'), $this->equalTo('text/html; charset=utf-8'));

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
        $mock = $this->getTransportMockEx('callLogic');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing expected header values.
     */
    public function testExpectedHeaderValuesPublicEx()
    {
        $mock = $this->getTransportMockEx('publicCall');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall()
    {
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

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
        // setup
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->expects($this->once())
            ->method('createSession');

        $mock->addRoute('private-method', 'privateMethod', 'GET', 'private_call');

        // test body and assertions
        $mock->getRouter()->callRoute('/private-method/');
    }

    /**
     * Testing creaetSession method
     */
    public function testCreateSession(): void
    {
        // setup and assertions
        $securityProvider = $this->getMockBuilder(MockProvider::class)
            ->setMethods([
            'createSession'
        ])
            ->disableOriginalConstructor()
            ->getMock();
        $securityProvider->expects($this->once())
            ->method('createSession');

        $transport = new ServiceHttpTransport($securityProvider);

        // test body
        $transport->createSession('some-token');
    }

    /**
     * Testing method
     */
    public function test(): void
    {
        // setup
        $e = [
            "message" => "msg",
            "code" => - 1
        ];
        $transport = $this->getTransportMockEx();

        // test body
        ob_start();
        $transport->outputException($e);
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"msg"', $content);
        $this->assertStringContainsString('-1', $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
