<?php
namespace Mezon\Service\Tests;

use Mezon\Service\ServiceModel;
use Mezon\Service\Service;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Transport\Tests\Headers;

/**
 *
 * @psalm-suppress PropertyNotSetInConstructor
 */
class InitCommonRoutesUnitTest extends ServiceUnitTests
{

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
                'connect',
                '"connect"'
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
    public function testInitCommonRoute(string $method, string $url, string $expected): void
    {
        // setup
        $securityProvider = new MockProvider();
        // TODO make it wotk for the ServiceHttpTransport
        $transport = new ServiceRestTransport($securityProvider);

        $logic = new TestingLogic($transport->getParamsFetcher(), $transport->getSecurityProvider(), new ServiceModel());

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
