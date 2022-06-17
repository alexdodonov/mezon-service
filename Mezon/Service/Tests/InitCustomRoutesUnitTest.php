<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceModel;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use PHPUnit\Framework\TestCase;
use Mezon\Headers\Layer;
use Mezon\Conf\Conf;
use Mezon\Service\Tests\Mocks\TestingLogic;
use Mezon\Service\Tests\Mocks\TestingBaseService;
use Mezon\Service\Tests\Mocks\NoRoutes\BaseService;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class InitCustomRoutesUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        // TODO move to the base class
        Conf::setConfigStringValue('headers/layer', 'mock');
    }

    /**
     * Testing initCustomRoutes for connect method
     */
    public function testInitCustomRoutes(): void
    {
        // setup
        Layer::setAllHeaders([]);
        $transport = new ServiceRestTransport();
        $logic = new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel());
        $transport->setServiceLogic($logic);
        new TestingBaseService($transport);

        // assertions
        $this->assertTrue($transport->routeExists('test'));
        $this->assertTrue($transport->routeExists('test2'));
    }

    /**
     * Testing PHP config load
     */
    public function testConstructorForUnexistingConfigs(): void
    {
        // setup
        $transport = new ServiceRestTransport();

        // test body
        $service = new BaseService($transport);

        // assertions
        $this->assertInstanceOf(BaseService::class, $service);
    }
}
