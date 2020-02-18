<?php

class RestExceptionUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Testing invalid construction
     */
    public function testConstructor()
    {
        $object = new \Mezon\Service\ServiceRestTransport\RestException('msg', 1, 200, 'body', 'http://ya.ru', [
            1,
            2
        ]);

        $this->assertEquals('msg', $object->getMessage(), 'Invalid message');
        $this->assertEquals(1, $object->getCode(), 'Invalid code');
        $this->assertEquals(200, $object->getHTTPCode(), 'Invalid HTTP code');
        $this->assertEquals('body', $object->getHTTPBody(), 'Invalid HTTP body');
        $this->assertEquals('http://ya.ru', $object->getURL(), 'Invalid URL');
        $this->assertEquals(2, count($object->getOptions()), 'Invalid options');
    }
}
