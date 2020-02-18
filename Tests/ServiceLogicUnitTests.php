<?php
namespace Mezon\Service\Tests;

/**
 * Class ServiceLogicUnitTests
 *
 * @package ServiceLogic
 * @subpackage ServiceLogicUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Base class for service logic unit tests
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceLogicUnitTests extends \Mezon\Service\Tests\ServiceBaseLogicUnitTests
{

    /**
     * Testing class name.
     *
     * @var string
     */
    protected $className = \Mezon\Service\ServiceLogic::class;

    /**
     * Method returns mock of the security provider
     */
    protected function getSecurityProviderMock()
    {
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceMockSecurityProvider::class)
            ->disableOriginalConstructor()
            ->setMethods([
            'connect',
            'setToken',
            'getParam',
            'validatePermit'
        ])
            ->getMock();

        $mock->method('connect')->will($this->returnValue('valuevalue'));
        $mock->method('setToken')->will($this->returnValue('token'));

        return $mock;
    }

    /**
     * Testing connection routine
     */
    public function testConnect()
    {
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        $result = $logic->connect();

        $this->assertEquals('valuevalue', $result['session_id'], 'Connection failed');
    }

    /**
     * Testing connection routine
     */
    public function testConnectWithEmptyParams()
    {
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(false), $securityProviderMock);

        $this->expectException(\Exception::class);
        $logic->connect();
    }

    /**
     * Testing setToken method
     */
    public function testSetToken()
    {
        // setup
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        // test body
        $result = $logic->setToken();

        // assertions
        $this->assertEquals('token', $result['session_id'], 'Setting token failed');
    }

    /**
     * Testing getSelfId method
     */
    public function testGetSelfId()
    {
        // setup
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        // test body
        $result = $logic->getSelfId();

        // assertions
        $this->assertEquals(1, $result['id'], 'Getting self id failed');
    }

    /**
     * Testing getSelfLogin method
     */
    public function testGetSelfLogin()
    {
        // setup
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        // test body
        $result = $logic->getSelfLogin();

        // assertions
        $this->assertEquals('admin@localhost', $result['login'], 'Getting self login failed');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs()
    {
        // setup
        $securityProviderMock = $this->getSecurityProviderMock();

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        // test body
        $result = $logic->loginAs();

        // assertions
        $this->assertEquals('value', $result['session_id'], 'Getting self login failed');
    }

    /**
     * Testing validatePermit method
     */
    public function testValidatePermit()
    {
        // setup
        $securityProviderMock = $this->getSecurityProviderMock();
        $securityProviderMock->method('validatePermit')->with($this->equalTo('value'), $this->equalTo('admin'));

        $serviceLogicClassName = $this->className;

        $logic = new $serviceLogicClassName(new \Mezon\Service\Tests\MockParamsFetcher(), $securityProviderMock);

        // test body and assertions
        $logic->validatePermit('admin');
        $this->addToAssertionCount(1);
    }
}
