<?php

/**
 * Unit tests for the class HttpRequestParams.
 */

define('SESSION_ID_FIELD_NAME', 'session_id');

$testHeaders = [];

function getallheaders()
{
    global $testHeaders;

    return $testHeaders;
}

/**
 *
 * @author Dodonov A.A.
 */
class HttpRequestParamsUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method constructs object to be tested.
     */
    protected function getRequestParamsMock()
    {
        $router = new \Mezon\Router\Router();

        return new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($router);
    }

    /**
     * Testing empty result of the get_http_request_headers method.
     */
    public function testGetHttpRequestHeaders()
    {
        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam('unexisting-param', 'default-value');

        $this->assertEquals('default-value', $param, 'Default value must be returned but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetSessionIdFromAuthorization()
    {
        global $testHeaders;
        $testHeaders = [
            'Authorization' => 'Basic author session id'
        ];

        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam(SESSION_ID_FIELD_NAME);

        $this->assertEquals('author session id', $param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetSessionIdFromCgiAuthorization()
    {
        global $testHeaders;
        $testHeaders = [
            'Cgi-Authorization' => 'Basic cgi author session id'
        ];

        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam(SESSION_ID_FIELD_NAME);

        $this->assertEquals('cgi author session id', $param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetUnexistingSessionId()
    {
        global $testHeaders;
        $testHeaders = [];

        $requestParams = $this->getRequestParamsMock();

        $this->expectException(Exception::class);
        $requestParams->getParam(SESSION_ID_FIELD_NAME);
    }

    /**
     * Testing getting parameter from custom header.
     */
    public function testGetParameterFromHeader()
    {
        global $testHeaders;
        $testHeaders = [
            'Custom-Header' => 'header value'
        ];

        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam('Custom-Header');

        $this->assertEquals('header value', $param, 'Header value must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_POST.
     */
    public function testGetParameterFromPost()
    {
        $_POST['post-parameter'] = 'post value';

        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam('post-parameter');

        $this->assertEquals('post value', $param, 'Value from $_POST must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_GET.
     */
    public function testGetParameterFromGet()
    {
        $_GET['get-parameter'] = 'get value';

        $requestParams = $this->getRequestParamsMock();

        $param = $requestParams->getParam('get-parameter');

        $this->assertEquals('get value', $param, 'Value from $_GET must be fetched but it was not');
    }
}
