<?php
namespace Mezon\Service\ServiceRestTransport\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Security\MockProvider;
use Mezon\Rest;
use Mezon\Headers\Layer;
use Mezon\Conf\Conf;
use Mezon\Service\Tests\FakeServiceLogic;
use Mezon\Transport\Tests\MockParamsFetcher;
use Mezon\Service\ServiceLogic;
use Mezon\Service\ServiceModel;
use Mezon\Functional\Fetcher;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceRestTransportUnitTest extends TestCase
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
     * Getting mock object
     *
     * @return object ServiceRestTransport mocked object
     */
    protected function getTransportMock(): object
    {
        $mock = $this->getMockBuilder(ServiceRestTransportMock::class)
            ->setConstructorArgs([
            new MockProvider()
        ])
            ->onlyMethods([
            'errorResponse'
        ])
            ->getMock();

        $mock->method('errorResponse')->willThrowException(new Rest\Exception('Msg', 0, 1, '1'));

        return $mock;
    }

    /**
     * Getting mock object.
     *
     * @return object ServiceLogic mocked object
     */
    protected function getServiceLogicMock(): object
    {
        return $this->getMockBuilder(TestingServiceLogicForRestTransport::class)
            ->disableOriginalConstructor()
            ->onlyMethods([
            'connect'
        ])
            ->getMock();
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
     * Setup method call
     *
     * @param string $methodName
     *            Method name
     * @return object Mock object
     */
    protected function setupMethod(string $methodName): object
    {
        $mock = $this->getTransportMock();

        $serviceLogic = new FakeServiceLogic();
        $mock->setServiceLogic($serviceLogic);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $mock->addRoute('public-logic', $methodName, 'GET', 'public_call');

        return $mock;
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall(): void
    {
        // setup
        Layer::setAllHeaders([]);
        $serviceTransport = new ServiceRestTransport();

        $serviceLogic = new FakeServiceLogic();
        $serviceTransport->setServiceLogic($serviceLogic);

        $serviceTransport->addRoute('public-logic', 'publicLogic', 'GET', 'public_call');

        // test body
        $result = $serviceTransport->getRouter()->callRoute('/public-logic/');

        // assertions
        $this->assertEquals('public', $result);
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

        // TODO create FakeServiceBaseLogic wich will work without `new ServiceModel()`
        $serviceLogic = new FakeServiceLogic();
        $mock->setServiceLogic($serviceLogic);

        $mock->addRoute('secure-logic', $methodName, 'GET', 'private_call');

        return $mock;
    }

    /**
     * Testing private call with createSession method
     */
    public function testPrivateCall(): void
    {
        // setup
        Layer::setAllHeaders([
            'Authentication' => 'Basic token'
        ]);
        $serviceTransport = new ServiceRestTransport();

        $serviceLogic = new FakeServiceLogic();
        $serviceTransport->setServiceLogic($serviceLogic);

        $serviceTransport->addRoute('secure-logic', 'secureLogic', 'GET', 'private_call');

        // test body
        $result = $serviceTransport->getRouter()->callRoute('/secure-logic/');

        // assertions
        $this->assertEquals('secure', $result);
    }

    /**
     * Testing errorResponse method
     */
    public function testErrorResponseException(): void
    {
        // setup
        $_SERVER['HTTP_HOST'] = 'http://service';
        $e = new \Exception('msg', 1);
        $Transport = new ServiceRestTransport();

        // test body
        $result = $Transport->errorResponse($e);

        // assertions
        $this->assertEquals('msg', $result['message']);
        $this->assertEquals(1, $result['code']);
        $this->assertEquals('http://service', $result['service']);
    }

    /**
     * Testing errorResponse method
     */
    public function testErrorResponseRestException(): void
    {
        // setup
        $_SERVER['HTTP_HOST'] = 'http://rest-service';
        $e = new Rest\Exception('msg', 1, 200, 'body');
        $Transport = new ServiceRestTransport();

        // test body
        $result = $Transport->errorResponse($e);

        // assertions
        $this->assertEquals('msg', $result['message']);
        $this->assertEquals(1, $result['code']);
        $this->assertEquals('http://rest-service', $result['service']);
        $this->assertEquals(200, $result['http_code']);
        $this->assertEquals('body', $result['http_body']);
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

        $mock->addRoute('connect', 'connect', 'GET', $mode, [
            'content_type' => 'application/json'
        ]);

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['r'] = 'connect';

        return $mock;
    }

    /**
     * Testing method outputException
     */
    public function testOutputException(): void
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

    /**
     * Testing that route will be called
     */
    public function testCallRoute(): void
    {
        // setup
        $transport = new ServiceRestTransport();
        $transport->setServiceLogic(new TestingServiceLogicForRestTransport());
        $transport->addRoute('/ok/', 'ok', 'GET', 'public_call');
        $_GET['r'] = 'ok';

        // test body
        ob_start();
        $transport->run();
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertEquals('"ok"', $content);
    }

    /**
     * Testing that header function is called once for each header
     */
    public function testSingleHeaderCall(): void
    {
        // setup
        $serviceLogic = new ServiceLogic(new MockParamsFetcher(), new MockProvider(), new ServiceModel());

        Layer::setAllHeaders([
            'Authentication' => 'Basic token'
        ]);

        // TODO make constructor Transport::__construct($serviceLogic = null) to make possible use it in this way: new ServiceRestTransport($serviceLogic)
        $transport = new ServiceRestTransport();
        $transport->setServiceLogic($serviceLogic);

        // test body
        $result = $transport->callLogic($serviceLogic, 'connect');

        // assertions
        $this->assertEquals(32, strlen(Fetcher::getField($result, 'session_id')));
    }
}
