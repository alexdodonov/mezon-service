<?php
namespace Mezon\Service\ServiceRestTransport;

use Mezon\Service\ServiceHttpTransport\ServiceHttpTransport;
use Mezon\Service\Transport;

/**
 * Class ServiceRestTransport
 *
 * @package Service
 * @subpackage ServiceRestTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/17)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * REST transport for all services.
 *
 * @author Dodonov A.A.
 */
class ServiceRestTransport extends ServiceHttpTransport
{
    
    /**
     *
     * {@inheritDoc}
     * @see Transport::logicCallPrepend()
     */
    protected function logicCallPrepend(): void
    {
        $this->header('Content-Type', 'application/json');
    }

    /**
     * Method calls route in transport specific way
     */
    protected function callRoute(): void
    {
        print(json_encode($this->getRouter()->callRoute($_GET['r'])));
    }

    /**
     * Error response compilator
     *
     * @param mixed $e
     *            exception object
     * @return array error data
     */
    public function errorResponse($e): array
    {
        $return = [
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'service' => $_SERVER['HTTP_HOST'] ?? 'unknown'
        ];

        $return['call_stack'] = $this->formatCallStack($e);

        if ($e instanceof \Mezon\Rest\Exception) {
            $return['http_code'] = $e->getHttpCode();
            $return['http_body'] = $e->getHttpBody();
        }

        return $return;
    }

    /**
     * Method outputs exception data
     *
     * @param array $e
     *            exception data
     */
    public function outputException(array $e): void
    {
        $this->header('Content-Type', 'application/json');

        print(json_encode($e));
    }
}
