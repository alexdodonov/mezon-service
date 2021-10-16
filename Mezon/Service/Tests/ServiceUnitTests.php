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
use Mezon\Service\ServiceBaseLogic;
use Mezon\Transport\Tests\MockParamsFetcher;

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
 * @psalm-suppress PropertyNotSetInConstructor
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
     * Method returns mock
     *
     * @return object Mock of the testing class
     */
    protected function getMock(): object
    {
        return $this->getMockBuilder($this->className)
            ->disableOriginalConstructor()
            ->onlyMethods([
            'run'
        ])
            ->getMock();
    }

    /**
     * Method creates logic
     *
     * @return ServiceLogic|string Service logic object
     */
    protected function getLogic()
    {
        $serviceTransport = new ServiceHttpTransport();

        return new ServiceLogic($serviceTransport->getParamsFetcher(), new MockProvider(), new ServiceModel());
    }

    /**
     * Testing launcher with security provider
     *
     * @see Service::launch
     */
    public function testLaunchWithSecurityProvider()
    {
        $localClassName = $this->className;

        $provider = new MockProvider();
        $service = $localClassName::launch(
            $this->className,
            new ServiceLogic(new MockParamsFetcher(), $provider, new ServiceModel()),
            new ServiceModel(),
            $provider,
            new ServiceRestTransport($provider),
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

        $provider = new MockProvider();
        $service = $localClassName::launch(
            $this->className,
            new ServiceLogic(new MockParamsFetcher(), $provider, new ServiceModel()),
            new ServiceModel(),
            $provider,
            new ServiceRestTransport($provider),
            false);

        $this->assertTrue(is_array($service->getLogics()), 'Array of logic objects was not created');
    }

    /**
     * Trying to run logic method from array
     */
    public function testServiceLogicFromArrayCanBeExecuted()
    {
        $localClassName = $this->className;

        $_GET['r'] = 'connect';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $provider = new MockProvider();
        $service = $localClassName::launch(
            $this->className,
            new ServiceLogic(new MockParamsFetcher(), new MockProvider(), new ServiceModel()),
            new ServiceModel(),
            $provider,
            new ServiceConsoleTransport($provider),
            false);

        $service->run();
        $this->assertTrue(true);
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
        $provider = new MockProvider();
        $service = $localClassName::start(
            get_class($mock),
            new ServiceLogic(new MockParamsFetcher(), $provider, new ServiceModel()),
            new ServiceModel(),
            $provider,
            new ServiceRestTransport($provider));

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
        $provider = new MockProvider();
        $service = $localClassName::start(
            $this->className,
            new ServiceLogic(new MockParamsFetcher(), $provider, new ServiceModel()),
            new ServiceModel(),
            $provider,
            new ServiceRestTransport($provider),
            false);

        // TODO create logic with StandartSecurityMethods but without model

        // assertions
        $this->assertInstanceOf($this->className, $service);
    }

    /**
     * Testing method
     */
    public function testExceptionWhileConstruction(): void
    {
        // setup and test body
        ob_start();
        $provider = new MockProvider();
        new ExceptionTestingService(
            new ServiceBaseLogic(new MockParamsFetcher(), $provider),
            new ServiceModel(),
            $provider,
            new TestingTransport($provider));
        $content = ob_get_contents();
        ob_end_clean();

        // assertions
        $this->assertStringContainsString("message", $content);
        $this->assertStringContainsString("code", $content);
        $this->assertTrue(is_array(json_decode($content, true)));
    }
}
