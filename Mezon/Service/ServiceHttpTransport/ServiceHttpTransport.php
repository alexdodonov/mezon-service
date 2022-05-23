<?php
namespace Mezon\Service\ServiceHttpTransport;

use Mezon\Service\Transport;
use Mezon\Transport\RequestParamsInterface;
use Mezon\Transport\HttpRequestParams;
use Mezon\Headers\Layer;

/**
 * Class ServiceHttpTransport
 *
 * @package Service
 * @subpackage ServiceHttpTransport
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/13)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * HTTP transport for all services
 *
 * @author Dodonov A.A.
 */
class ServiceHttpTransport extends Transport
{

    /**
     * Method creates parameters fetcher
     *
     * @return RequestParamsInterface paremeters fetcher
     */
    protected function createFetcher(): RequestParamsInterface
    {
        return new HttpRequestParams($this->getRouter());
    }

    /**
     * Method outputs HTTP header
     *
     * @param string $header
     *            header name
     * @param string $value
     *            header value
     * @codeCoverageIgnore
     */
    protected function header(string $header, string $value): void
    {
        Layer::addHeader($header, $value);
    }

    /**
     *
     * {@inheritdoc}
     * @see Transport::logicCallPrepend()
     */
    protected function logicCallPrepend(): void
    {
        $this->header('Content-Type', 'text/html; charset=utf-8');
    }

    /**
     * Method outputs exception data
     *
     * @param array $e
     *            exception data
     */
    public function outputException(array $e): void
    {
        $this->header('Content-Type', 'text/html; charset=utf-8');

        print(json_encode($e));
    }
}
