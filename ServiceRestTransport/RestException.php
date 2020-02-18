<?php
namespace Mezon\Service\ServiceRestTransport;

/**
 * Class RestException
 *
 * @package Mezon
 * @subpackage RestException
 * @author Dodonov A.A.
 * @version v.1.0 (2019/08/15)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Class for rest exceptions
 */
class RestException extends \Exception
{

    /**
     * HTTP response code
     *
     * @var int
     */
    protected $httpCode = 0;

    /**
     * HTTP response body
     *
     * @var string
     */
    protected $httpBody = '';

    /**
     * HTTP response URL
     *
     * @var string
     */
    protected $url = '';

    /**
     * HTTP request options
     *
     * @var string
     */
    protected $options = false;

    /**
     * Constructor
     *
     * @param string $message
     *            Error description
     * @param int $code
     *            Code of the error
     * @param int $httpCode
     *            Response HTTP code
     * @param string $httpBody
     *            Body of the response
     * @param string $url
     *            Request URL
     * @param array $options
     *            Request options
     */
    public function __construct(
        string $message,
        int $code,
        string $httpCode,
        string $httpBody,
        string $url = '',
        array $options = [])
    {
        parent::__construct($message, $code);

        $this->httpCode = $httpCode;

        $this->httpBody = $httpBody;

        $this->url = $url;

        $this->options = $options;
    }

    /**
     * Method returns HTTP code
     *
     * @return int HTTP code
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Method returns HTTP body
     *
     * @return string HTTP body
     */
    public function getHttpBody(): string
    {
        return $this->httpBody;
    }

    /**
     * Method returns URL
     *
     * @return string URL
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Method returns request options
     *
     * @return array Request options
     */
    public function getOptions(): array
    {
        return $this->options;
    }
}
