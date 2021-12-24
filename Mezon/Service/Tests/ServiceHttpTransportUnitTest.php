<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Transport\HttpRequestParams;
use Mezon\Security\MockProvider;
use Mezon\Transport\Tests\MockParamsFetcher;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceHttpTransportUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object
     */
    protected function getServiceLogicMock(): object
    {
        return $this->getMockBuilder(TestingServiceLogicForHttpTransport::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
            'connect'
        ])
            ->getMock();
    }

    /**
     * Getting mock object
     *
     * @return object ServiceRestTransport mocked object
     */
    protected function getTransportMock(): object
    {
        $mock = $this->getMockBuilder(ServiceHttpTransport::class)
            ->setConstructorArgs([
            new MockProvider()
        ])
            ->onlyMethods([
            'header',
            'createSession'
        ])
            ->getMock();

        $mock->expects($this->once())
            ->method('header');

        $paramFetcher = new MockParamsFetcher('token');

        $mock->setParamsFetcher($paramFetcher);

        return $mock;
    }

    /**
     * Testing that security provider was set
     */
    public function testSecurityProviderInitObject(): void
    {
        $transport = new ServiceHttpTransport(new MockProvider());
        $this->assertInstanceOf(MockProvider::class, $transport->getSecurityProvider());
    }

    /**
     * Testing that header function is called once for each header
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
     * Testing that header function is called once for each header
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
     * Testing expected header values
     */
    public function testExpectedHeaderValues(): void
    {
        $mock = $this->getTransportMock();

        $mock->method('header')->with($this->equalTo('Content-Type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callLogic($serviceLogic, 'connect');
    }

    /**
     * Testing expected header values
     */
    public function testExpectedHeaderValuesPublic(): void
    {
        $mock = $this->getTransportMock();

        $mock->method('header')->with($this->equalTo('Content-Type'), $this->equalTo('text/html; charset=utf-8'));

        $serviceLogic = $this->getServiceLogicMock();

        $mock->callPublicLogic($serviceLogic, 'connect');
    }

    /**
     * Getting tricky mock object
     *
     * @return object mock object
     */
    protected function getTransportMockEx(string $mode = 'publicCall'): object
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
     * Testing expected header values
     */
    public function testExpectedHeaderValuesEx(): void
    {
        $mock = $this->getTransportMockEx('callLogic');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing expected header values
     */
    public function testExpectedHeaderValuesPublicEx(): void
    {
        $mock = $this->getTransportMockEx('publicCall');

        $mock->getRouter()->callRoute($_GET['r']);
    }

    /**
     * Testing public call without createSession method
     */
    public function testPublicCall(): void
    {
        $mock = $this->getTransportMock();

        $mock->setServiceLogic($this->getServiceLogicMock());

        $mock->expects($this->never())
            ->method('createSession');

        $mock->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        $mock->getRouter()->callRoute('/public-method/');
    }

    /**
     * Testing private call with createSession method
     */
    public function testPrivateCallNoException(): void
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
            ->onlyMethods([
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
     * Testing trace outputting
     */
    public function testTraceOutput(): void
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
