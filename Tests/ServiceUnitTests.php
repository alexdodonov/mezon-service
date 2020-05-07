<?php
namespace Mezon\Service\Tests;

/**
 * Class ServiceUnitTests
 *
 * @package Service
 * @subpackage ServiceUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */
define('AS_STRING', 1);
define('AS_OBJECT', 2);

class ExceptionTestingService extends \Mezon\Service\Service
{

    protected function initCustomRoutes(): void
    {
        // not loading routes from config
    }

    protected function initCommonRoutes(): void
    {
        // and here we emulate error
        throw (new \Exception("msg", 1));
    }
}

class TestingTransport extends \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport
{

    // @codeCoverageIgnoreStart
    protected function die(): void
    {}
    // @codeCoverageIgnoreEnd
}

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    protected $className = \Mezon\Service\Service::class;

    /**
     * Testing initialization of the security provider
     */
    public function testInitSecurityProviderDefault()
    {
        $service = new $this->className(
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf(
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $service->getTransport()->getSecurityProvider());

        $service = new $this->className(
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $service->getTransport()->getSecurityProvider());

        $service = new $this->className(
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            $this->getSecurityProvider(AS_OBJECT),
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $service->getTransport()->getSecurityProvider());
    }

    /**
     * Testing initialization of the service model
     */
    public function testInitServiceModel()
    {
        $service = new $this->className(
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()[0]->getModel());

        $service = new $this->className(
            $this->getLogic(),
            \Mezon\Service\ServiceModel::class,
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()[0]->getModel());

        $service = new $this->className(
            $this->getLogic(),
            new \Mezon\Service\ServiceModel(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()[0]->getModel());
    }

    /**
     * Method returns mock
     *
     * @return object Mock of the testing class
     */
    protected function getMock(): object
    {
        return $this->getMockBuilder($this->className)
            ->disableOriginalConstructor()
            ->setMethods([
            'run'
        ])
            ->getMock();
    }

    /**
     * Method creates logic
     *
     * @return \Mezon\Service\ServiceLogic|string Service logic object
     */
    protected function getLogic()
    {
        $serviceTransport = new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport();

        return new \Mezon\Service\ServiceLogic(
            $serviceTransport->getParamsFetcher(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new \Mezon\Service\ServiceModel());
    }

    /**
     * Method creates security provider
     *
     * @param int $mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceSecurityProviderInterface|string Service security provider object
     */
    protected function getSecurityProvider(int $mode)
    {
        if ($mode == AS_STRING) {
            return \Mezon\Service\ServiceMockSecurityProvider::class;
        } else {
            return new \Mezon\Service\ServiceMockSecurityProvider();
        }
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service\Service::launch
     */
    public function testLaunchWithTransport()
    {
        $localClassName = $this->className;
        $mock = $this->getMock();

        // implicit
        $service = $localClassName::launch(get_class($mock));
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());

        // explicit string
        $service = $localClassName::launch(
            get_class($mock),
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());

        // explicit object
        $service = $localClassName::launch(
            get_class($mock),
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport());
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());
    }

    /**
     * Testing launcher with security provider
     *
     * @see \Mezon\Service\Service::launch
     */
    public function testLaunchWithSecurityProvider()
    {
        $localClassName = $this->className;

        $service = $localClassName::launch(
            $this->className,
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            false);

        $this->assertInstanceOf(
            \Mezon\Service\ServiceMockSecurityProvider::class,
            $service->getTransport()->getSecurityProvider());
    }

    /**
     * Trying to construct service from array of logics
     */
    public function testCreateServiceLogicFromArray()
    {
        $localClassName = $this->className;

        $service = $localClassName::launch(
            $this->className,
            [
                \Mezon\Service\ServiceLogic::class
            ],
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            false);

        $this->assertTrue(is_array($service->getLogic()), 'Array of logic objects was not created');
    }

    /**
     * Trying to run logic method from array
     */
    public function testServiceLogicFromArrayCanBeExecuted()
    {
        $localClassName = $this->className;

        $_GET['r'] = 'connect';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $service = $localClassName::launch(
            $this->className,
            [
                \Mezon\Service\ServiceLogic::class
            ],
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class,
            false);

        $service->run();
        $this->addToAssertionCount(1);
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service\Service::start
     */
    public function testStartWithTransport()
    {
        // setup
        $localClassName = $this->className;
        $mock = $this->getMock();

        // test body
        $service = $localClassName::start(get_class($mock));

        // assertions
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());
    }

    /**
     * Testing service creation without running it
     */
    public function testCreateServiceWithoutRunningIt(): void
    {
        // setup
        $localClassName = $this->className;
        $mock = $this->getMock();
        $mock->expects($this->never())
            ->method('run');

        // test body
        $service = $localClassName::start(
            $this->className,
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            false);

        // assertions
        $this->assertInstanceOf(\Mezon\Service\Service::class, $service);
    }

    /**
     * Testing method
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and test body
        ob_start();
        new ExceptionTestingService(
            \Mezon\Service\ServiceLogic::class,
            \Mezon\Service\ServiceModel::class,
            \Mezon\Service\ServiceMockSecurityProvider::class,
            \TestingTransport::class);
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString("message", $content);
        $this->assertStringContainsString("code", $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
