<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class ExceptionMBStringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test if exception is thrown when extension `simpleXML` is not loaded
     */
    public function testExceptionMBString()
    {
        if (!extension_loaded('mbstring')) {
            $this->expectException('\vipnytt\SitemapParser\Exceptions\SitemapParserException');
            new SitemapParser('SitemapParser');
        }
    }
}
