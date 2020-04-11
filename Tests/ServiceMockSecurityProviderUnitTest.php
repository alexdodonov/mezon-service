<?php

class ServiceMockSecurityProviderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing session creation.
     */
    public function testCreateSession1(): void
    {
        // setup
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        // test body
        $token = $provider->createSession(md5(1));

        // assertions
        $this->assertEquals(32, strlen($token));
    }

    /**
     * Testing session creation with already created token
     */
    public function testCreateSession2(): void
    {
        // setup
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        // test body
        $token = $provider->createSession('token');

        // assertions
        $this->assertEquals('token', $token);
    }
    
    /**
     * Testing session with token creation
     */
    public function testCreateSession3(): void
    {
        // setup
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        // test body
        $token = $provider->createSession('');

        // assertions
        $this->assertEquals(32, strlen($token));
    }

    /**
     * Testing validatePermit method
     */
    public function testValidatePermit(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $provider->validatePermit('token', 'permit');

        $this->addToAssertionCount(1);
    }

    /**
     * Testing connect method
     */
    public function testConnect(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $hash = $provider->connect('l', 'p');

        $this->assertEquals(32, strlen($hash));
    }

    /**
     * Testing hasPermit method
     */
    public function testHasPermit(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $this->assertTrue($provider->hasPermit('t', 'p'));
    }
}
