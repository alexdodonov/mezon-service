<?php
namespace Mezon\Service\Tests;

use Mezon\Functional\Fetcher;
use PHPUnit\Framework\TestCase;

/**
 * Class ServiceTests
 *
 * @package Service
 * @subpackage ServiceTests
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Predefined set of tests for service
 *
 * @author Dodonov A.A.
 */
abstract class ServiceTests extends TestCase
{

    /**
     * Session id
     *
     * @var string
     */
    protected $sessionId = '';

    /**
     * Server path
     *
     * @var string
     */
    protected static $serverPath = '';

    /**
     * Headers
     *
     * @var ?array
     */
    protected $headers = null;

    /**
     * Method asserts for errors and warnings in the html code
     *
     * @param string $content
     *            Asserting content
     * @param string $message
     *            Message to be displayed in case of error
     */
    protected function assertErrors($content, $message): void
    {
        if (strpos($content, 'Warning') !== false || strpos($content, 'Error') !== false ||
            strpos($content, 'Fatal error') !== false || strpos($content, 'Access denied') !== false ||
            strpos($content, "doesn't exist in statement") !== false) {
            throw (new \Exception($message . "\r\n" . $content));
        }

        $this->assertTrue(true);
    }

    /**
     * Method asserts JSON
     *
     * @param mixed $jsonResult
     *            Result of the call
     * @param string $result
     *            Raw result of the call
     */
    protected function assertJsonResponse($jsonResult, string $result): void
    {
        if ($jsonResult === null && $result !== '') {
            $this->fail("JSON result is invalid because of:\r\n$result");
        }

        if (isset($jsonResult->message)) {
            print("message    : " . $jsonResult->message . "\r\n");
            print("code       : " . ($jsonResult->code ?? '') . "\r\n");
            print("call_stack : ");
            print(json_encode($jsonResult->call_stack ?? 'not provided'));
            $this->fail();
        }
    }

    /**
     * Method sends post request
     *
     * @param array $data
     *            Request data
     * @param string $url
     *            Requesting endpoint
     * @return mixed Request result
     */
    protected function postHttpRequest(array $data, string $url)
    {
        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->sessionId !== '' ? "Cgi-Authorization: Basic " . $this->sessionId . "\r\n" : '') .
                ($this->headers !== null ? implode("\r\n", $this->headers) . "\r\n" : ''),
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $this->assertErrors($result, 'Request have returned warnings/errors');

        return json_decode($result);
    }

    /**
     * Method prepares GET request options
     *
     * @return array GET request options
     */
    protected function prepareGetOptions(): array
    {
        return [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->sessionId !== '' ? "Cgi-Authorization: Basic " . $this->sessionId . "\r\n" : '') .
                ($this->headers !== null ? implode("\r\n", $this->headers) . "\r\n" : ''),
                'method' => 'GET'
            ]
        ];
    }

    /**
     * Method sends GET request
     *
     * @param string $url
     *            Requesting URL
     * @return mixed Result off the request
     */
    protected function getHttpRequest(string $url)
    {
        $options = $this->prepareGetOptions();

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        $this->assertErrors($result, 'Request have returned warnings/errors');

        return json_decode($result);
    }

    /**
     * Method returns array ['login' => '' , 'password' => ''], for connecting to service
     *
     * @return array Test data
     */
    protected abstract function getUserData(): array;

    /**
     * Method performs valid connect
     *
     * @return mixed Result of the connection
     */
    protected function validConnect()
    {
        $data = $this->getUserData();

        $url = self::$serverPath . '/connect/';

        $result = $this->postHttpRequest($data, $url);

        if (isset($result->session_id) !== false) {
            $this->sessionId = $result->session_id;
        }

        return $result;
    }

    /**
     * Testing API connection
     */
    public function testValidConnect(): void
    {
        // setup and test body
        $result = $this->validConnect();

        // assertions
        $this->assertNotEquals($result, null, 'Connection failed');

        if (isset($result->session_id) === false) {
            $this->assertEquals(true, false, 'Field "session_id" was not set');
        }

        $this->sessionId = $result->session_id;
    }

    /**
     * Testing API invalid connection
     */
    public function testInvalidConnect(): void
    {
        // setup
        $data = $this->getUserData();
        $data['password'] = '1234';
        $url = self::$serverPath . '/connect/';

        // test body
        $result = $this->postHttpRequest($data, $url);

        // assertions
        $this->assertTrue(isset($result->message));
        $this->assertTrue(isset($result->code));
        $this->assertTrue($result->code == - 1 || $result->code == 4);
    }

    /**
     * Testing setting valid token
     */
    public function testSetValidToken(): void
    {
        // setup
        $this->testValidConnect();

        $data = [
            'token' => $this->sessionId
        ];

        $url = self::$serverPath . '/token/' . $this->sessionId . '/';

        // test body
        $result = $this->postHttpRequest($data, $url);

        // assertions
        $this->assertEquals(isset($result->session_id), true, 'Connection failed');
    }

    /**
     * Testing setting invalid token
     */
    public function testSetInvalidToken(): void
    {
        // setup
        $this->testValidConnect();

        $data = [
            'token' => ''
        ];

        $url = self::$serverPath . '/token/unexisting/';

        // test body
        $result = $this->postHttpRequest($data, $url);

        // assertions
        $this->assertObjectHasAttribute('message', $result);
    }

    /**
     * Testing login under another user
     */
    public function testLoginAs(): void
    {
        // setup
        $this->testValidConnect();

        $data = [
            'login' => 'alexey@dodonov.ru'
        ];
        $url = self::$serverPath . '/login-as/';
        $this->postHttpRequest($data, $url);

        // test body
        $url = self::$serverPath . '/self/login/';
        $result = $this->getHttpRequest($url);

        // assertions
        $this->assertEquals('alexey@dodonov.ru', Fetcher::getField($result, 'login'));
    }
}
