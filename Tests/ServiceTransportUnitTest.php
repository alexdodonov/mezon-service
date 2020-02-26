<?php

/**
 * Tests for the class ServiceTransport.
 */
class FakeService implements \Mezon\Service\ServiceBaseLogicInterface
{

    public function actionHelloWorld()
    {
        return 1;
    }
}

class ConcreteFetcher implements \Mezon\Service\ServiceRequestParamsInterface
{

    public function getParam($param, $default = false)
    {
        return 1;
    }
}

class ConcreteServiceTransport extends \Mezon\Service\ServiceTransport
{

    public function createFetcher(): \Mezon\Service\ServiceRequestParamsInterface
    {
        return new ConcreteFetcher();
    }
}

/**
 * Fake service logic.
 *
 * @author Dodonov A.A.
 */
class FakeServiceLogic extends \Mezon\Service\ServiceLogic
{

    public function __construct(\Mezon\Router\Router &$router)
    {
        parent::__construct(
            new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($router),
            new \Mezon\Service\ServiceMockSecurityProvider());
    }

    public function test()
    {
        return 'test';
    }
}

/**
 *
 * @author Dodonov A.A.
 */
class ServiceTransportUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing constructor.
     */
    public function testConstructor(): void
    {
        $serviceTransport = new ConcreteServiceTransport();

        $this->assertInstanceOf(\Mezon\Router\Router::class, $serviceTransport->getRouter(), 'Router was not created');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogic(): void
    {
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());
        $serviceTransport->addRoute('test', 'test', 'GET');

        $result = $serviceTransport->getRouter()->callRoute('test');

        $this->assertEquals('test', $result, 'Invalid route execution result');
    }

    /**
     * Testing simple calling of the logic's method.
     */
    public function testGetServiceLogicPublic(): void
    {
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());
        $serviceTransport->addRoute('test', 'test', 'GET', 'public_call');

        $result = $serviceTransport->getRouter()->callRoute('test');

        $this->assertEquals('test', $result, 'Invalid public route execution result');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicFromArray(): void
    {
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = [
            new FakeServiceLogic($serviceTransport->getRouter())
        ];
        $serviceTransport->addRoute('test', 'test', 'GET');

        $_GET['r'] = 'test';
        $_REQUEST['HTTP_METHOD'] = 'GET';
        ob_start();
        $serviceTransport->run();
        $output = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('test', $output, 'Invalid route execution result for multyple logics');
    }

    /**
     * Testing calling of the logic's method from array.
     */
    public function testGetServiceLogicWithUnexistingMethod(): void
    {
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());

        $this->expectException(Exception::class);
        $serviceTransport->addRoute('unexisting', 'unexisting', 'GET');
    }

    /**
     * Testing call stack formatter.
     */
    public function testFormatCallStack(): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();
        $exception = new Exception('Error message', - 1);

        // test body
        $format = $serviceTransport->errorResponse($exception);

        // assertions
        $this->assertEquals(5, count($format), 'Invalid formatter');
    }

    /**
     * Data provider
     *
     * @return string[][][] Data set
     */
    public function dataProviderForTestInvalidLoadRoute()
    {
        return [
            [
                [
                    'route' => '/route/',
                    'callback' => 'test'
                ]
            ],
            [
                [
                    'route' => '/route/'
                ]
            ],
            [
                [
                    'callback' => 'test'
                ]
            ]
        ];
    }

    /**
     * Testing 'load_route' method
     */
    public function testLadRoute(): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());

        // test body
        $serviceTransport->loadRoute([
            'route' => '/route/',
            'callback' => 'test'
        ]);

        // assertions
        $this->assertTrue($serviceTransport->routeExists('/route/'));
    }

    /**
     * Testing 'loadRoute' method with unexisting logic
     *
     * @dataProvider dataProviderForTestInvalidLoadRoute
     */
    public function testInvalidLoadRoute(array $route): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = null;

        // test body
        $this->expectException(Exception::class);
        $serviceTransport->loadRoute($route);
    }

    /**
     * Testing load_routes method
     */
    public function testLoadRoutes(): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());

        // test body
        $serviceTransport->loadRoutes([
            [
                'route' => '/route/',
                'callback' => 'test'
            ]
        ]);

        // assertions
        $this->assertTrue($serviceTransport->routeExists('/route/'));
    }

    /**
     * Testing fetchActions method
     */
    public function testFetchActions(): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();
        $serviceTransport->serviceLogic = new FakeServiceLogic($serviceTransport->getRouter());

        // test body
        $serviceTransport->fetchActions(new FakeService());

        // assertions
        $this->assertTrue($serviceTransport->routeExists('/hello-world/'));
    }

    /**
     * Testing 'getParam' method
     */
    public function testGetParam(): void
    {
        // setup
        $serviceTransport = new ConcreteServiceTransport();

        // test body and assertions
        $this->assertEquals(1, $serviceTransport->getParam('param'));
    }
}
