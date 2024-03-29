<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceModel;
use Mezon\Service\Service;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use PHPUnit\Framework\TestCase;
use Mezon\Headers\Layer;
use Mezon\Conf\Conf;
use Mezon\Service\Tests\Mocks\TestingLogic;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class InitCommonRoutesUnitTest extends TestCase
{

    /**
     *
     * {@inheritdoc}
     * @see TestCase::setUp()
     */
    protected function setUp(): void
    {
        Conf::setConfigStringValue('headers/layer', 'mock');
    }

    /**
     * Testing initCommonRoutes for connect method
     */
    public function testInitCommonConnectRoute(): void
    {
        // setup
        Layer::setAllHeaders([]);
        $transport = new ServiceRestTransport();

        $logic = new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel());

        $transport->setServiceLogic($logic);

        $service = new Service($transport);

        // running default methods
        ob_start();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_GET['r'] = 'connect';
        $service->run();

        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString('"connect"', $content);
    }

    /**
     * Testing data
     *
     * @return array
     */
    public function initCommonRouteDataProvider(): array
    {
        return [
            [
                'POST',
                'token/newToken',
                '"newToken"'
            ],
            [
                'GET',
                'self/id',
                '{"id":1}'
            ],
            [
                'GET',
                'self/login',
                '{"login":"admin@localhost"}'
            ]
        ];
    }

    /**
     * Testing initCommonRoute
     *
     * @param string $method
     *            method
     * @param string $url
     *            url
     * @param string $expectedexpected
     *            string
     * @dataProvider initCommonRouteDataProvider
     */
    public function testInitCommonPrivateRoute(string $method, string $url, string $expected): void
    {
        // setup
        Layer::setAllHeaders([
            'Authentication' => 'Basic some-token'
        ]);
        $transport = new ServiceRestTransport();

        $logic = new TestingLogic($transport->getParamsFetcher(), new MockProvider(), new ServiceModel());

        $transport->setServiceLogic($logic);

        $service = new Service($transport);

        // running default methods
        ob_start();
        $_SERVER['REQUEST_METHOD'] = $method;
        $_GET['r'] = $url;
        $service->run();
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString($expected, $content);
    }
}
