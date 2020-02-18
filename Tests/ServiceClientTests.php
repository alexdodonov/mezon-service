<?php
namespace Mezon\Service\Tests;

/**
 * Class ServiceClientTests
 *
 * @package ServiceClient
 * @subpackage ServiceClientTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Common unit tests for ServiceClient and all derived client classes
 *
 * @author Dodonov A.A.
 * @group baseTests
 */
class ServiceClientTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Client class name
     */
    protected $clientClassName = '';

    /**
     * Existing user's login
     *
     * @var string
     */
    protected $existingLogin = '';

    /**
     * Constructor
     *
     * @param string $existingLogin
     */
    public function __construct(string $existingLogin)
    {
        parent::__construct();

        $this->existingLogin = $existingLogin;
    }

    /**
     * Method creates client object
     *
     * @param string $password
     */
    protected function constructClient(string $password = 'root')
    {
        return new $this->clientClassName($this->existingLogin, $password);
    }

    /**
     * Testing API connection
     */
    public function testValidConnect()
    {
        $client = $this->construct_client();

        $this->assertNotEquals($client->getSessionId(), false, 'Connection failed');
        $this->assertEquals($client->Login, $this->existingLogin, 'Login was not saved');
    }

    /**
     * Testing invalid API connection
     */
    public function testInValidConnect()
    {
        $this->expectException(\Exception::class);
        $this->construct_client('1234567');
    }

    /**
     * Testing setting valid token
     */
    public function testSetValidToken()
    {
        $client = $this->construct_client();

        $newClient = new $this->clientClassName();
        $newClient->setToken($client->getSessionId());

        $this->assertNotEquals($newClient->getSessionId(), false, 'Token was not set(1)');
    }

    /**
     * Testing setting valid token and login
     */
    public function testSetValidTokenAndLogin()
    {
        $client = $this->construct_client();

        $newClient = new $this->clientClassName();
        $newClient->setToken($client->getSessionId(), 'alexey@dodonov.none');

        $this->assertNotEquals($newClient->getSessionId(), false, 'Token was not set(2)');
        $this->assertNotEquals($newClient->getStoredLogin(), false, 'Login was not saved');
    }

    /**
     * Testing setting invalid token
     */
    public function testSetInValidToken()
    {
        $client = new $this->clientClassName();

        $this->expectException(\Exception::class);
        $client->setToken('unexistingtoken');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs()
    {
        $client = $this->construct_client();

        try {
            $client->loginAs($this->existingLogin);
        } catch (\Exception $e) {
            $this->assertEquals(0, 1, 'Login was was not called properly');
        }
    }

    /**
     * Testing loginAs method with failed call
     */
    public function testFailedLoginAs()
    {
        $client = $this->construct_client();

        $this->expectException(\Exception::class);
        $client->loginAs('alexey@dodonov.none');
    }

    /**
     * Testing situation that loginAs will not be called after the connect() call with the same login
     */
    public function testSingleLoginAs()
    {
        $this->assertEquals(0, 1, 'Test was not created');
    }
}
