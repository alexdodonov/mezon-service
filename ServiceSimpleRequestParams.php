<?php
namespace Mezon\Service;

/**
 * Class ServiceSimpleRequestParams
 *
 * @package Service
 * @subpackage ServiceSimpleRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/31)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
class ServiceSimpleRequestParams implements \Mezon\Service\ServiceRequestParamsInterface
{

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

        if (isset($headers[$param])) {
            $return = $headers[$param];
        } elseif (isset($_POST[$param])) {
            $return = $_POST[$param];
        } elseif (isset($_GET[$param])) {
            $return = $_GET[$param];
        }

        return $return;
    }
}
