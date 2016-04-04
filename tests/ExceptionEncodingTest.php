<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class ExceptionEncodingTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown when extension `simpleXML` is not loaded
     */
    public function testExceptionEncoding()
    {
        if (!mb_internal_encoding('UTF-8')) {
            $this->expectException('\vipnytt\SitemapParser\Exceptions\SitemapParserException');
            new SitemapParser('SitemapParser');
        }
    }
}
