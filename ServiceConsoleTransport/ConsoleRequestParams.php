<?php
namespace Mezon\Service\ServiceConsoleTransport;

/**
 * Class ConsoleRequestParams
 *
 * @package ServiceConsoleTransport
 * @subpackage ConsoleRequestParams
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/12)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Request params fetcher
 */
class ConsoleRequestParams extends \Mezon\Transport\RequestParams
{

    /**
     * Method returns session id from HTTP header
     *
     * @return string Session id
     * @codeCoverageIgnore
     */
    protected function getSessionId()
    {
        return '';
    }

    /**
     * Method returns parameter
     *
     * @param string $param
     *            - parameter name
     * @param mixed $default
     *            - default value
     * @return mixed Parameter value
     */
    public function getParam($param, $default = false)
    {
        global $argv;

        if (isset($argv[$param])) {
            return $argv[$param];
        }

        return $default;
    }
}
