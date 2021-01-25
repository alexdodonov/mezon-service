<?php
namespace Mezon\Service\Tests;

use PHPUnit\Framework\TestCase;
use Mezon\Service\Service;
use Mezon\Service\ServiceLogic;
use Mezon\Service\ServiceModel;
use Mezon\Security\MockProvider;
use Mezon\Service\ServiceRestTransport\ServiceRestTransport;
use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport;
use Mezon\Service\Tests\Mocks\TestingTransport;

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

/**
 * Common service unit tests
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceUnitTests extends TestCase
{

    /**
     * Service class name
     *
     * @var string
     */
    protected $className = Service::class;

    /**
     * Testing initialization of the security provider
     */
    public function testInitSecurityProviderDefault()
    {
        $service = new $this->className(
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceRestTransport::class);
        $this->assertInstanceOf(MockProvider::class, $service->getTransport()
            ->getSecurityProvider());

        $service = new $this->className(
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            new ServiceRestTransport());
        $this->assertInstanceOf(MockProvider::class, $service->getTransport()
            ->getSecurityProvider());

        $service = new $this->className(
            ServiceLogic::class,
            ServiceModel::class,
            $this->getSecurityProvider(AS_OBJECT),
            new ServiceRestTransport());
        $this->assertInstanceOf(MockProvider::class, $service->getTransport()
            ->getSecurityProvider());
    }

    /**
     * Testing initialization of the service model
     */
    public function testInitServiceModel()
    {
        $service = new $this->className(
            ServiceLogic::class,
            ServiceModel::class,
            new MockProvider(),
            new ServiceRestTransport());
        $this->assertInstanceOf(ServiceModel::class, $service->getLogic()[0]->getModel());

        $service = new $this->className(
            $this->getLogic(),
            ServiceModel::class,
            new MockProvider(),
            new ServiceRestTransport());
        $this->assertInstanceOf(ServiceModel::class, $service->getLogic()[0]->getModel());

        $service = new $this->className(
            $this->getLogic(),
            new ServiceModel(),
            new MockProvider(),
            new ServiceRestTransport());
        $this->assertInstanceOf(ServiceModel::class, $service->getLogic()[0]->getModel());
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
        $serviceTransport = new ServiceHttpTransport();

        return new ServiceLogic($serviceTransport->getParamsFetcher(), new MockProvider(), new ServiceModel());
    }

    /**
     * Method creates security provider
     *
     * @param int $mode
     *            - Creation mode
     * @return ServiceSecurityProviderInterface|string Service security provider object
     */
    protected function getSecurityProvider(int $mode)
    {
        if ($mode == AS_STRING) {
            return MockProvider::class;
        } else {
            return new MockProvider();
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
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());

        // explicit string
        $service = $localClassName::launch(
            get_class($mock),
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceRestTransport::class);
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());

        // explicit object
        $service = $localClassName::launch(
            get_class($mock),
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            new ServiceRestTransport());
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());
    }

    /**
     * Testing launcher with security provider
     *
     * @see Service::launch
     */
    public function testLaunchWithSecurityProvider()
    {
        $localClassName = $this->className;

        $service = $localClassName::launch(
            $this->className,
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceRestTransport::class,
            false);

        $this->assertInstanceOf(MockProvider::class, $service->getTransport()
            ->getSecurityProvider());
    }

    /**
     * Trying to construct service from array of logics
     */
    public function testCreateServiceLogicFromArray()
    {
        $localClassName = $this->className;

        $service = $localClassName::launch($this->className, [
            ServiceLogic::class
        ], ServiceModel::class, MockProvider::class, ServiceRestTransport::class, false);

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

        $service = $localClassName::launch($this->className, [
            ServiceLogic::class
        ], ServiceModel::class, MockProvider::class, ServiceConsoleTransport::class, false);

        $service->run();
        $this->addToAssertionCount(1);
    }

    /**
     * Testing launcher with transport
     *
     * @see Service::start
     */
    public function testStartWithTransport()
    {
        // setup
        $localClassName = $this->className;
        $mock = $this->getMock();

        // test body
        $service = $localClassName::start(get_class($mock));

        // assertions
        $this->assertInstanceOf(ServiceRestTransport::class, $service->getTransport());
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
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            ServiceRestTransport::class,
            false);

        // assertions
        $this->assertInstanceOf(Service::class, $service);
    }

    /**
     * Testing method
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and test body
        ob_start();
        new ExceptionTestingService(
            ServiceLogic::class,
            ServiceModel::class,
            MockProvider::class,
            TestingTransport::class);
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString("message", $content);
        $this->assertStringContainsString("code", $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
