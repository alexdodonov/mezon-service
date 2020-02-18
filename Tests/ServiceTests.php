<?php
namespace Mezon\Service\Tests;

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
 * @group baseTests
 */
class ServiceTests extends \PHPUnit\Framework\TestCase
{

    /**
     * Session id.
     */
    protected $sessionId = false;

    /**
     * Server path.
     */
    protected $serverPath = false;

    /**
     * Headers.
     *
     * @var string
     */
    protected $headers = false;

    /**
     * Constructor.
     *
     * @param string $service
     *            - Service name.
     */
    public function __construct(string $service)
    {
        parent::__construct();

        $this->serverPath = \Mezon\DnsClient\DnsClient::resolveHost($service);
    }

    /**
     * Method asserts for errors and warnings in the html code.
     *
     * @param string $content
     *            - Asserting content.
     * @param string $message
     *            - Message to be displayed in case of error.
     */
    protected function assertErrors($content, $message)
    {
        if (strpos($content, 'Warning') !== false || strpos($content, 'Error') !== false ||
            strpos($content, 'Fatal error') !== false || strpos($content, 'Access denied') !== false ||
            strpos($content, "doesn't exist in statement") !== false) {
            throw (new \Exception($message . "\r\n" . $content));
        }

        $this->addToAssertionCount(1);
    }

    /**
     * Method asserts JSON.
     *
     * @param mixed $jSONResult
     *            - Result of the call;
     * @param string $result
     *            - Raw result of the call.
     */
    protected function assertJson($jSONResult, string $result)
    {
        if ($jSONResult === null && $result !== '') {
            throw (new \Exception("JSON result is invalid because of:\r\n$result"));
        }

        if (isset($jSONResult->message)) {
            throw (new \Exception($jSONResult->message, $jSONResult->code));
        }
    }

    /**
     * Method sends post request.
     *
     * @param array $data
     *            - Request data;
     * @param string $uRL
     *            - Requesting endpoint.
     * @return mixed Request result.
     */
    protected function postHttpRequest(array $data, string $uRL)
    {
        $options = [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->sessionId !== false ? "Cgi-Authorization: Basic " . $this->sessionId . "\r\n" : '') .
                ($this->headers !== false ? implode("\r\n", $this->headers) . "\r\n" : ''),
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($uRL, false, $context);

        $this->assertErrors($result, 'Request have returned warnings/errors');

        $jSONResult = json_decode($result);

        $this->assertJson($jSONResult, $result);

        return $jSONResult;
    }

    /**
     * Method prepares GET request options.
     */
    protected function prepareGetOptions()
    {
        return [
            'http' => [
                'header' => "Content-type: application/x-www-form-urlencoded\r\n" .
                "User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0\r\n" .
                ($this->sessionId !== false ? "Cgi-Authorization: Basic " . $this->sessionId . "\r\n" : '') .
                ($this->headers !== false ? implode("\r\n", $this->headers) . "\r\n" : ''),
                'method' => 'GET'
            ]
        ];
    }

    /**
     * Method sends GET request
     *
     * @param string $uRL
     *            Requesting URL
     * @return mixed Result off the request
     */
    protected function getHtmlRequest(string $uRL)
    {
        $options = $this->prepareGetOptions();

        $context = stream_context_create($options);
        $result = file_get_contents($uRL, false, $context);

        $this->assertErrors($result, 'Request have returned warnings/errors');

        $jSONResult = json_decode($result);

        $this->assertJson($jSONResult, $result);

        return $jSONResult;
    }

    /**
     * Method returns test data
     *
     * @return array Test data
     */
    protected function getUserData(): array
    {
        return [
            'login' => 'alexey@dodonov.pro',
            'password' => 'root'
        ];
    }

    /**
     * Method performs valid connect.
     *
     * @return mixed Result of the connection.
     */
    protected function validConnect()
    {
        $data = $this->getUserData();

        $uRL = $this->serverPath . '/connect/';

        $result = $this->postHttpRequest($data, $uRL);

        if (isset($result->session_id) !== false) {
            $this->sessionId = $result->session_id;
        }

        return $result;
    }

    /**
     * Testing API connection.
     */
    public function testValidConnect()
    {
        // authorization
        $result = $this->validConnect();

        $this->assertNotEquals($result, null, 'Connection failed');

        if (isset($result->session_id) === false) {
            $this->assertEquals(true, false, 'Field "session_id" was not set');
        }

        $this->sessionId = $result->session_id;
    }

    /**
     * Testing API invalid connection.
     */
    public function testInvalidConnect()
    {
        // authorization
        $data = $this->getUserData();
        $data['password'] = '1234';

        $uRL = $this->serverPath . '/connect/';

        $this->expectException(\Exception::class);
        $this->postHttpRequest($data, $uRL);
    }

    /**
     * Testing setting valid token.
     */
    public function testSetValidToken()
    {
        $this->testValidConnect();

        $data = [
            'token' => $this->sessionId
        ];

        $uRL = $this->serverPath . '/token/' . $this->sessionId . '/';

        $result = $this->postHttpRequest($data, $uRL);

        $this->assertEquals(isset($result->session_id), true, 'Connection failed');
    }

    /**
     * Testing setting invalid token.
     */
    public function testSetInvalidToken()
    {
        try {
            $this->testValidConnect();

            $data = [
                'token' => ''
            ];

            $uRL = $this->serverPath . '/token/unexisting/';

            $this->postHttpRequest($data, $uRL);
        } catch (\Exception $e) {
            // set token method either throws exception or not
            // both is correct behaviour
            $this->assertEquals($e->getMessage(), 'Invalid session token', 'Invalid error message');
            $this->assertEquals($e->getCode(), 2, 'Invalid error code');
        }
    }

    /**
     * Testing login under another user
     */
    public function testLoginAs()
    {
        // setup
        $this->testValidConnect();

        // test body
        $data = [
            'login' => 'alexey@dodonov.none'
        ];

        $uRL = $this->serverPath . '/login-as/';

        $this->postHttpRequest($data, $uRL);

        // assertions
        $uRL = $this->serverPath . '/self/login/';

        $result = $this->get_html_request($uRL);

        $this->assertEquals(
            'alexey@dodonov.none',
            \Mezon\Functional\Functional::getField($result, 'login'),
            'Session user must be alexey@dodonov.none');
    }
}
