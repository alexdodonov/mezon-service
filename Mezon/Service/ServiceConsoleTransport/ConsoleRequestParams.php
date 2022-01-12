<?php
namespace Mezon\Service\ServiceConsoleTransport;

use Mezon\Transport\RequestParams;

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
class ConsoleRequestParams extends RequestParams
{

    /**
     * Method returns session id from HTTP header
     *
     * @return string session id
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
     *            parameter name
     * @param mixed $default
     *            default value
     * @return mixed parameter value
     */
    public function getParam($param, $default = false)
    {
        global $argv;

        if (isset($argv[(int) $param])) {
            return $argv[(int) $param];
        }

        return $default;
    }
}
