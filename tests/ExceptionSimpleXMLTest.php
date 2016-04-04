<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class ExceptionSimpleXMLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown when extension `simpleXML` is not loaded
     */
    public function testExceptionSimpleXML()
    {
        if (!extension_loaded('simplexml')) {
            $this->expectException('\vipnytt\SitemapParser\Exceptions\SitemapParserException');
            new SitemapParser('SitemapParser');
        }
    }
}
