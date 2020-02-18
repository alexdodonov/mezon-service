<?php
namespace Mezon\Service\ServiceHttpTransport;

/**
 * Class HttpRequestParams
 *
 * @package ServiceHttpTransport
 * @subpackage HttpRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/07)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher.
 */
class HttpRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

    /**
     * Router of the transport
     *
     * @var \Mezon\Router\Router
     */
    protected $router = false;

    /**
     * Constructor
     *
     * @param \Mezon\Router\Router $router
     *            Router object
     */
    public function __construct(\Mezon\Router\Router &$router)
    {
        $this->router = $router;
    }

    /**
     * Fetching auth token from headers
     *
     * @param array $headers
     *            Request headers
     * @return string Session id
     */
    protected function getSessionIdFromHeaders(array $headers)
    {
        if (isset($headers['Authorization'])) {
            $token = str_replace('Basic ', '', $headers['Authorization']);

            return $token;
        } elseif (isset($headers['Cgi-Authorization'])) {
            $token = str_replace('Basic ', '', $headers['Cgi-Authorization']);

            return $token;
        }

        throw (new \Exception('Invalid session token', 2));
    }

    /**
     * Method returns list of the request's headers
     *
     * @return array[string] Array of headers
     */
    protected function getHttpRequestHeaders(): array
    {
        $headers = getallheaders();

        return $headers === false ? [] : $headers;
    }

    /**
     * Method returns session id from HTTP header
     *
     * @return string Session id
     */
    protected function getSessionId()
    {
        $headers = $this->getHttpRequestHeaders();

        return $this->getSessionIdFromHeaders($headers);
    }

    /**
     * Method returns request parameter
     *
     * @param string $param
     *            parameter name
     * @param mixed $default
     *            default value
     * @return mixed Parameter value
     */
    public function getParam($param, $default = false)
    {
        $headers = $this->getHttpRequestHeaders();

        $return = $default;

        if ($param == 'session_id') {
            $return = $this->getSessionId();
        } elseif ($this->router->hasParam($param)) {
            $return = $this->router->getParam($param);
        } elseif (isset($headers[$param])) {
            $return = $headers[$param];
        } elseif (isset($_POST[$param])) {
            $return = $_POST[$param];
        } elseif (isset($_GET[$param])) {
            $return = $_GET[$param];
        }

        return $return;
    }
}
