<?php

class ServiceMockSecurityProviderUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing session creation.
     */
    public function testCreateSession1(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->createSession();

        $this->assertEquals(32, strlen($token));
    }

    /**
     * Testing session creation with already created token
     */
    public function testCreateSession2(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->createSession('token');

        $this->assertEquals('token', $token);
    }

    /**
     * Testing setting token
     */
    public function testSetToken(): void
    {
        $provider = new \Mezon\Service\ServiceMockSecurityProvider();

        $token = $provider->setToken('token');

        $this->assertEquals('token', $token);
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
