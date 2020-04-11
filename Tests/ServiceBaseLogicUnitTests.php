<?php
namespace Mezon\Service\Tests;

/**
 * Service logic utin tests
 *
 * @package ServiceLogic
 * @subpackage ServiceBaseLogicUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Mock model
 *
 * @author Dodonov A.A.
 */
class MockModel extends \Mezon\Service\ServiceModel
{
}

/**
 * Base class for service logic unit tests.
 *
 * @author Dodonov A.A.
 */
class ServiceBaseLogicUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing class name.
     *
     * @var string
     */
    protected $className = \Mezon\Service\ServiceBaseLogic::class;

    /**
     * Method tests creation of the logis's parts
     *
     * @param object $logic
     *            ServiceLogic object
     * @param string $msg
     *            Error message
     */
    protected function checkLogicParts(object $logic, string $msg): void
    {
        $this->assertInstanceOf(MockParamsFetcher::class, $logic->getParamsFetcher(), $msg);
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $logic->getSecurityProvider(), $msg);
        $this->assertInstanceOf(MockModel::class, $logic->getModel(), $msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct1(): void
    {
        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new MockParamsFetcher(), new \Mezon\Service\ServiceMockSecurityProvider());

        $msg = 'Construction failed for default model';

        $this->assertInstanceOf(MockParamsFetcher::class, $logic->getParamsFetcher(), $msg);
        $this->assertInstanceOf(\Mezon\Service\ServiceMockSecurityProvider::class, $logic->getSecurityProvider(), $msg);
        $this->assertEquals(null, $logic->getModel(), $msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct2(): void
    {
        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(
            new MockParamsFetcher(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            new MockModel());

        $msg = 'Construction failed for defined model object';

        $this->checkLogicParts($logic, $msg);
    }

    /**
     * Testing connect method
     */
    public function testConstruct3(): void
    {
        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(
            new MockParamsFetcher(),
            new \Mezon\Service\ServiceMockSecurityProvider(),
            MockModel::class);

        $msg = 'Construction failed for defined model name';

        $this->checkLogicParts($logic, $msg);
    }
}
