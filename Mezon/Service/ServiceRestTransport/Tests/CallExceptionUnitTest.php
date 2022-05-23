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
use Mezon\Service\ServiceModel;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class CallExceptionUnitTest extends TestCase
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
     * Method setups transport
     *
     * @param string $method method
     * @return ServiceRestTransport transport
     */
    protected function setupPublicMethod(string $method): ServiceRestTransport
    {
        $serviceTransport = new ServiceRestTransport();

        // TODO move to the base class
        $serviceLogic = new FakeRestServiceLogic();
        $serviceTransport->setServiceLogic($serviceLogic);

        $serviceTransport->addRoute('public-logic', $method, 'GET', 'public_call');

        return $serviceTransport;
    }

    /**
     * Setup method call
     *
     * @param string $method method
     * @return ServiceRestTransport transport
     */
    protected function setupPrivateMethod(string $method): ServiceRestTransport
    {
        $serviceTransport = new ServiceRestTransport();

        // TODO move to the base class
        $serviceLogic = new FakeRestServiceLogic();
        $serviceTransport->setServiceLogic($serviceLogic);

        $serviceTransport->addRoute('secure-logic', $method, 'GET', 'private_call');

        return $serviceTransport;
    }

    /**
     * Testing public call with exception throwing
     */
    public function testPublicCallException(): void
    {
        // setup
        Layer::setAllHeaders([]);
        $serviceTransport = $this->setupPublicMethod('exception');

        // test body and assertions
        $result = $serviceTransport->getRouter()->callRoute('/public-logic/');

        // assertions
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('service', $result);
        $this->assertArrayHasKey('call_stack', $result);
    }

    /**
     * Testing private call with exception throwing
     */
    public function testPrivateCallException(): void
    {
        // setup
        Layer::setAllHeaders([
            'Authentication' => 'Basic token'
        ]);
        $serviceTransport = $this->setupPrivateMethod('exception');

        // test body
        $result = $serviceTransport->getRouter()->callRoute('/secure-logic/');

        // assertions
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('code', $result);
        $this->assertArrayHasKey('service', $result);
        $this->assertArrayHasKey('call_stack', $result);
    }
}
