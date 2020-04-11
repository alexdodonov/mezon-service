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
        $router->addRoute('/test/[i:rparam]/', function () {}, 'GET');
        $router->callRoute('/test/111/');

        return new \Mezon\Service\ServiceHttpTransport\HttpRequestParams($router);
    }

    /**
     * Testing empty result of the get_http_request_headers method.
     */
    public function testGetHttpRequestHeaders()
    {
        // setup
        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam('unexisting-param', 'default-value');

        // assertions
        $this->assertEquals('default-value', $param, 'Default value must be returned but it was not');
    }

    /**
     * Testing getting parameter
     */
    public function testGetSessionIdFromAuthorization()
    {
        // setup
        global $testHeaders;
        $testHeaders = [
            'Authorization' => 'Basic author session id'
        ];
        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam(SESSION_ID_FIELD_NAME);

        // assertions
        $this->assertEquals('author session id', $param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter
     */
    public function testGetSessionIdFromAuthentication()
    {
        // setup
        global $testHeaders;
        $testHeaders = [
            'Authentication' => 'Basic author session id'
        ];
        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam(SESSION_ID_FIELD_NAME);

        // assertions
        $this->assertEquals('author session id', $param, 'Session id must be fetched but it was not');
    }

    /**
     * Testing getting parameter.
     */
    public function testGetSessionIdFromCgiAuthorization()
    {
        // setup
        global $testHeaders;
        $testHeaders = [
            'Cgi-Authorization' => 'Basic cgi author session id'
        ];
        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam(SESSION_ID_FIELD_NAME);

        // assertions
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
        // setup
        global $testHeaders;
        $testHeaders = [
            'Custom-Header' => 'header value'
        ];

        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam('Custom-Header');

        // assertions
        $this->assertEquals('header value', $param, 'Header value must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_POST.
     */
    public function testGetParameterFromPost()
    {
        // setup
        $_POST['post-parameter'] = 'post value';

        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam('post-parameter');

        // assertions
        $this->assertEquals('post value', $param, 'Value from $_POST must be fetched but it was not');
    }

    /**
     * Testing getting parameter from $_GET.
     */
    public function testGetParameterFromGet()
    {
        // setup
        $_GET['get-parameter'] = 'get value';

        $requestParams = $this->getRequestParamsMock();

        // test body
        $param = $requestParams->getParam('get-parameter');

        // assertions
        $this->assertEquals('get value', $param, 'Value from $_GET must be fetched but it was not');
    }

    /**
     * Testing method
     */
    public function testGettingParametersFromRoute(): void
    {
        // setup
        $requestParams = $this->getRequestParamsMock();

        // test body
        $result = $requestParams->getParam('rparam');

        // assertions
        $this->assertEquals(111, $result);
    }
}
