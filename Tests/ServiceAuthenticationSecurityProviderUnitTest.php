<?php

class ServiceAuthenticationSecurityProviderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing getLoginFieldName
     */
    public function testGetLoginFieldName(): void
    {
        // setup
        $securityProvider = new \Mezon\Service\ServiceAuthenticationSecurityProvider();

        // assertions
        $this->assertEquals('login', $securityProvider->getLoginFieldName());
    }

    /**
     * Testing getSessionIdFieldName
     */
    public function testGetSessionIdFieldName(): void
    {
        // setup
        $securityProvider = new \Mezon\Service\ServiceAuthenticationSecurityProvider();

        // assertions
        $this->assertEquals('session-id', $securityProvider->getSessionIdFieldName());
    }

    /**
     * Testing getSelfLogin
     */
    public function testGetSelfLogin(): void
    {
        // setup
        $securityProvider = new \Mezon\Service\ServiceAuthenticationSecurityProvider();
        $_SESSION['session-user-login'] = 'session-login';

        // assertions
        $this->assertEquals('session-login', $securityProvider->getSelfLogin());
    }

    /**
     * Testing getSelfId
     */
    public function testGetSelfId(): void
    {
        // setup
        $securityProvider = new \Mezon\Service\ServiceAuthenticationSecurityProvider();
        $_SESSION['session-user-id'] = 111;

        // assertions
        $this->assertEquals(111, $securityProvider->getSelfId());
    }

    /**
     * Testing createSession
     */
    public function testCreateSession(): void
    {
        // setup
        $_SESSION['session-user-login'] = 'login';
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceAuthenticationSecurityProvider::class)
            ->setMethods([
            'sessionId'
        ])
            ->getMock();

        // test body
        $token = $mock->createSession('created-token');

        // assertions
        $this->assertEquals('created-token', $token);
    }

    /**
     * Testing createSession
     */
    public function testCreateSessionException(): void
    {
        // setup
        unset($_SESSION['session-user-login']);
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceAuthenticationSecurityProvider::class)
            ->setMethods([
            'sessionId'
        ])
            ->getMock();

        // assertions
        $this->expectException(\Exception::class);

        // test body
        $mock->createSession('created-token');
    }

    /**
     * Testing connect method
     */
    public function testConnect(): void
    {
        // setup
        $mock = $this->getMockBuilder(\Mezon\Service\ServiceAuthenticationSecurityProvider::class)
            ->setMethods([
            'sessionId'
        ])
            ->getMock();

        // test body
        $mock->connect('login@localhost', 'root');

        // assertions
        $this->assertEquals('login@localhost', $_SESSION['session-user-login']);
        $this->assertEquals(1, $_SESSION['session-user-id']);
    }
}