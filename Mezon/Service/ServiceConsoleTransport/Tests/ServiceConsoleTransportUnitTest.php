<?php
namespace Mezon\Service\ServiceConsoleTransport\Tests;

use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Security\MockProvider;
use Mezon\Service\Tests\FakeServiceLogic;
use Mezon\Transport\Tests\MockParamsFetcher;
use Mezon\Service\ServiceModel;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class ServiceConsoleTransportUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Getting mock object.
     *
     * @return object ServiceConsoleTransport mocked object.
     */
    protected function getTransportMock(): object
    {
        // TODO remove this mock
        return $this->getMockBuilder(ServiceConsoleTransport::class)
            ->setConstructorArgs([
            new MockProvider()
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
            ->onlyMethods([
            'connect'
        ])
            ->getMock();
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCall(): void
    {
        // setup
        $mock = $this->getTransportMock();

        $serviceLogic = $this->getServiceLogicMock();

        // test body
        $result = $mock->callLogic($serviceLogic, 'connect');

        // assertions
        $this->assertEquals('', $result);
    }

    /**
     * Testing that header function is called once for each header.
     */
    public function testSingleHeaderCallPublic(): void
    {
        // setup
        $mock = $this->getTransportMock();

        $serviceLogic = $this->getServiceLogicMock();

        // test body
        $result = $mock->callPublicLogic($serviceLogic, 'connect');

        // assertions
        $this->assertEquals('', $result);
    }

    /**
     * Testing public call without createSession method.
     */
    public function testPublicCall(): void
    {
        // setup
        $_GET['r'] = 'public-logic';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $serviceTransport = new ServiceConsoleTransport();

        $serviceTransport->setServiceLogic(new FakeServiceLogic());

        $serviceTransport->addRoute('public-logic', 'publicLogic', 'GET', 'public_call');

        // test body
        $serviceTransport->run();

        // assertions
        $this->assertEquals('public', ServiceConsoleTransport::$result);
    }

    /**
     * Testing private call with createSession method.
     */
    public function testPrivateCall(): void
    {
        // setup
        $_GET['r'] = '/private-method/';
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $serviceTransport = new ServiceConsoleTransport();

        // TODO make FakeServiceLogic::__construct default parameters for all args to make this string shorter
        // and use new FakeServiceLogic() instead of new FakeServiceLogic(new MockParamsFetcher(), new MockProvider(), new ServiceModel())
        $serviceTransport->setServiceLogic(new FakeServiceLogic());

        $serviceTransport->addRoute('private-method', 'secureLogic', 'GET', 'private_call');

        // test body
        $serviceTransport->run();

        // assertions
        $this->assertEquals('secure', ServiceConsoleTransport::$result);
    }
}
