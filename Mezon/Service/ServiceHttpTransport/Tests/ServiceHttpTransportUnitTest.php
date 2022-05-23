<?php
namespace Mezon\Service\ServiceHttpTransport\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Security\MockProvider;
use Mezon\Service\Tests\FakeServiceLogic;
use Mezon\Service\ServiceModel;
use Mezon\Conf\Conf;
use Mezon\Headers\Layer;

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
        Conf::setConfigStringValue('headers/layer', 'mock');
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
        // TODO remove one usage of this mock
        $mock = $this->getMockBuilder(ServiceHttpTransport::class)
            ->setConstructorArgs([
            new MockProvider()
        ])
            ->onlyMethods([
            'header'
        ])
            ->getMock();

        $mock->expects($this->once())
            ->method('header');

        return $mock;
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
        // setup
        Layer::setAllHeaders([
            'Authentication' => 'Basic token'
        ]);

        $serviceTransport = new ServiceHttpTransport();

        $serviceTransport->setServiceLogic(
            new TestingServiceLogicForHttpTransport(
                $serviceTransport->getParamsFetcher(),
                new MockProvider(),
                new ServiceModel()));

        $serviceTransport->addRoute('private-method', 'privateMethod', 'GET', 'secure_call');

        // test body
        $_GET['r'] = 'private-method';
        ob_start();
        $serviceTransport->run();
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('private', $content);
        $this->assertTrue(in_array('text/html; charset=utf-8', Layer::getAllHeaders()));
        $this->assertArrayHasKey('Content-Type', Layer::getAllHeaders());
        $this->assertCount(2, Layer::getAllHeaders());
    }

    /**
     * Testing expected header values
     */
    public function testExpectedHeaderValuesPublicEx(): void
    {
        // setup
        Layer::setAllHeaders([]);

        $serviceTransport = new ServiceHttpTransport();

        $serviceTransport->setServiceLogic(
            new TestingServiceLogicForHttpTransport(
                $serviceTransport->getParamsFetcher(),
                new MockProvider(),
                new ServiceModel()));

        $serviceTransport->addRoute('public-method', 'publicMethod', 'GET', 'public_call');

        // test body
        $_GET['r'] = 'public-method';
        ob_start();
        $serviceTransport->run();
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('public', $content);
        $this->assertTrue(in_array('text/html; charset=utf-8', Layer::getAllHeaders()));
        $this->assertArrayHasKey('Content-Type', Layer::getAllHeaders());
        $this->assertCount(1, Layer::getAllHeaders());
    }

    /**
     * Testing public call without createSession method
     */
    public function testPublicCall(): void
    {
        // setup
        Layer::setAllHeaders([]);

        $serviceTransport = new ServiceHttpTransport();

        $serviceTransport->setServiceLogic(new FakeServiceLogic());

        $serviceTransport->addRoute('public-logic', 'publicLogic', 'GET', 'public_call');

        // test body
        $result = $serviceTransport->getRouter()->callRoute('/public-logic/');

        // assertions
        $this->assertEquals('public', $result);
    }

    /**
     * Testing private call with createSession method
     */
    public function testPrivateCallNoException(): void
    {
        // setup
        Layer::setAllHeaders([
            'Authentication' => 'Basic token'
        ]);

        $serviceTransport = new ServiceHttpTransport();

        $serviceTransport->setServiceLogic(new FakeServiceLogic());

        $serviceTransport->addRoute('secure-method', 'secureLogic', 'GET', 'private_call');

        // test body
        $result = $serviceTransport->getRouter()->callRoute('/secure-method/');

        // assertions
        $this->assertEquals('secure', $result);
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
