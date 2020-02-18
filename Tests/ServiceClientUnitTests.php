<?php
namespace Mezon\Service\Tests;

/**
 * Class ServiceClientUnitTests
 *
 * @package ServiceClient
 * @subpackage ServiceClientUnitTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/09/20)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Basic tests for service client
 *
 * @author Dodonov A.
 * @group baseTests
 */
class ServiceClientUnitTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Client class name
     */
    protected $clientClassName = \Mezon\Service\ServiceClient::class;

    /**
     * Common setup for all tests
     */
    public function setUp(): void
    {
        \Mezon\DnsClient\DnsClient::clear();
        \Mezon\DnsClient\DnsClient::setService('existing-service', 'https://existing-service.com');
    }

    /**
     * Method creates mock for the service client
     *
     * @param array $methods
     *            mocking methods
     * @return object Mock
     */
    protected function getServiceClientRawMock(
        array $methods = [
            'sendPostRequest',
            'sendGetRequest',
            'sendPutRequest',
            'sendDeleteRequest'
        ]): object
    {
        return $this->getMockBuilder($this->clientClassName)
            ->setMethods($methods)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Method creates mock with setup
     *
     * @param string $dataFile
     *            File name with testing data
     * @return object Mock object
     */
    protected function getServiceClientMock(string $dataFile): object
    {
        $mock = $this->getServiceClientRawMock([
            'sendRequest'
        ]);

        $mock->method('sendRequest')->will(
            $this->returnValue(json_decode(file_get_contents(__DIR__ . '/conf/' . $dataFile . '.json'), true)));

        return $mock;
    }

    /**
     * Testing construction with login and password
     */
    public function testConstructWithLogin(): void
    {
        // setup
        $mock = $this->getServiceClientMock('construct-with-login');

        // test body
        $mock->__construct('http://example.com/', 'login', 'password');

        // assertions
        $this->assertEquals('login', $mock->getStoredLogin(), 'Login was not set');
        $this->assertEquals('session id', $mock->getToken(), 'SessionId was not set');
    }

    /**
     * Testing constructor
     */
    public function testSetHeader(): void
    {
        // setup
        $client = new $this->clientClassName('http://example.com/');

        // test body and assertions
        $this->assertEquals('', $client->getService(), 'Field was init but it must not');
    }

    /**
     * Checking exception throwing if the service was not found
     */
    public function testNoServiceFound(): void
    {
        $this->expectException(\Exception::class);

        new $this->clientClassName('auth');
    }

    /**
     * Testing that service was found.
     */
    public function testServiceFound(): void
    {
        $client = new $this->clientClassName('existing-service');

        $this->assertEquals('existing-service', $client->getService(), 'Field was init but it must not');
    }

    /**
     * Data provider for the test testSendRequest
     *
     * @return array test data
     */
    public function sendRequestDataProvider(): array
    {
        return [
            [
                'sendGetRequest'
            ],
            [
                'sendPostRequest'
            ],
            [
                'sendPutRequest'
            ],
            [
                'sendDeleteRequest'
            ]
        ];
    }

    /**
     * Testing send[Post|Get|Put|Delete]Request
     *
     * @param string $methodName
     *            testing method name
     * @dataProvider sendRequestDataProvider
     */
    public function testSendRequest(string $methodName): void
    {
        $mock = $this->getServiceClientMock('test-request');

        $result = $mock->$methodName('http://ya.ru', []);

        $this->assertEquals(1, $result->result);
    }

    /**
     * Testing setToken method
     */
    public function testSetToken(): void
    {
        // setup
        $mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body
        $mock->setToken('token', 'login');

        // assertions
        $this->assertEquals('token', $mock->getToken(), 'SessionId was not set');
        $this->assertEquals('login', $mock->getStoredLogin(), 'Login was not set');
    }

    /**
     * Testing getToken method
     */
    public function testGetToken(): void
    {
        // setup
        $mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body
        $sessionId = $mock->getToken();

        // assertions
        $this->assertEquals('', $sessionId, 'Invalid session id');
    }

    /**
     * Testing setToken method
     */
    public function testSetTokenException(): void
    {
        // setup
        $mock = $this->getServiceClientRawMock(); // we need this function, as we need mock without any extra setup

        // test body and assertions
        $this->expectException(\Exception::class);
        $mock->setToken('');
    }

    /**
     * Testing getSelfId method
     */
    public function testGetSelfId(): void
    {
        // setup
        $mock = $this->getServiceClientMock('self-id');

        // test body
        $selfId = $mock->getSelfId();

        // assertions
        $this->assertEquals('123', $selfId, 'Invalid self id');
    }

    /**
     * Testing getSelfLogin method
     */
    public function testGetSelfLogin(): void
    {
        // setup
        $mock = $this->getServiceClientMock('self-login');

        // test body
        $selfLogin = $mock->getSelfLogin();

        // assertions
        $this->assertEquals('admin', $selfLogin, 'Invalid self login');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAsWithInvalidSessionId(): void
    {
        // setup
        $mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body and assertions
        $this->expectException(\Exception::class);

        $mock->loginAs('registered', 'login');
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAsWithInvalidSessionId2(): void
    {
        // setup
        $mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body
        $mock->loginAs('registered', 'id');

        // assertions
        $this->assertFalse($mock->getStoredLogin());
    }

    /**
     * Testing loginAs method
     */
    public function testLoginAs(): void
    {
        // setup
        $mock = $this->getServiceClientMock('login-as');

        // test body
        $mock->loginAs('registered', 'login');

        // assertions
        $this->assertEquals('session-id', $mock->getToken(), 'Invalid self login');
    }

    /**
     * Testing construction with login and password and invalid session_id
     */
    public function testConstructWithLoginAndInvalidSessionId(): void
    {
        // setup
        $mock = $this->getServiceClientMock('login-with-invalid-session-id');

        // test body and assertions
        $this->expectException(\Exception::class);
        $mock->__construct('http://example.com/', 'login', 'password');
    }
}
