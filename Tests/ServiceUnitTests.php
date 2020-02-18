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
     * Constructor
     *
     * @param string $className
     *            - Class name to be tested
     */
    public function __construct(string $className = \Mezon\Service\Service::class)
    {
        parent::__construct();

        $this->className = $className;
    }

    /**
     * Testing initialization of the security provider
     */
    public function testInitSecurityProviderDefault()
    {
        $service = new $this->className(\Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $service->getTransport()->securityProvider);

        $service = new $this->className(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            $this->getSecurityProvider(AS_STRING));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $service->getTransport()->securityProvider);

        $service = new $this->className(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            $this->getSecurityProvider(AS_OBJECT));
        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $service->getTransport()->securityProvider);
    }

    /**
     * Testing initialization of the service model
     */
    public function testInitServiceModel()
    {
        $service = new $this->className(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_STRING));
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()
            ->getModel());

        $service = new $this->className(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_OBJECT),
            'ServiceModel');
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()
            ->getModel());

        $service = new $this->className(
            new \Mezon\Service\ServiceRestTransport\ServiceRestTransport(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            $this->getLogic(AS_OBJECT),
            new \Mezon\Service\ServiceModel());
        $this->assertInstanceOf(\Mezon\Service\ServiceModel::class, $service->getLogic()
            ->getModel());
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
     * @param int $mode
     *            - Creation mode
     * @return \Mezon\Service\ServiceLogic|string Service logic object
     */
    protected function getLogic(int $mode)
    {
        if ($mode == AS_STRING) {
            return \Mezon\Service\ServiceLogic::class;
        }
        if ($mode == AS_OBJECT) {
            $serviceTransport = new \Mezon\Service\ServiceHttpTransport\ServiceHttpTransport();
            return new \Mezon\Service\ServiceLogic(
                $serviceTransport->getParamsFetcher(),
                new \stdClass(),
                new \Mezon\Service\ServiceModel());
        }
        return null;
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
        }
        if ($mode == AS_OBJECT) {
            return new \Mezon\Service\ServiceMockSecurityProvider();
        }
        return null;
    }

    /**
     * Testing launcher with transport
     *
     * @see \Mezon\Service\Service::launch
     */
    public function testLaunchWithRransport()
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
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class);
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());

        // explicit object
        $service = $localClassName::launch(
            get_class($mock),
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
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $this->getSecurityProvider(AS_STRING),
            $this->getLogic(AS_STRING),
            \Mezon\Service\ServiceModel::class,
            false);

        $this->assertInstanceOf($this->getSecurityProvider(AS_STRING), $service->getTransport()->securityProvider);
    }

    /**
     * Trying to construct service from array of logics
     */
    public function testCreateServiceLogicFromArray()
    {
        $localClassName = $this->className;

        $service = $localClassName::launch(
            $this->className,
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $this->getSecurityProvider(AS_STRING),
            [
                $this->getLogic(AS_STRING)
            ],
            \Mezon\Service\ServiceModel::class,
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
            \Mezon\Service\ServiceConsoleTransport\ServiceConsoleTransport::class,
            $this->getSecurityProvider(AS_STRING),
            [
                $this->getLogic(AS_STRING)
            ],
            \Mezon\Service\ServiceModel::class,
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
        $localClassName = $this->className;
        $mock = $this->getMock();

        // implicit
        $service = $localClassName::start(get_class($mock));
        $this->assertInstanceOf(
            \Mezon\Service\ServiceRestTransport\ServiceRestTransport::class,
            $service->getTransport());
    }
}
