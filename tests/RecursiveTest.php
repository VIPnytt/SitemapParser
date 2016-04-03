<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class RecursiveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testRecursive($url)
    {
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parseRecursive($url);
        $this->assertTrue(is_array($parser->getSitemaps()));
        $this->assertTrue(is_array($parser->getURLs()));
        $this->assertTrue(count($parser->getSitemaps()) > 1 || count($parser->getURLs()) > 100);
        foreach ($parser->getSitemaps() as $url => $tags) {
            $this->assertTrue(is_string($url));
            $this->assertTrue(is_array($tags));
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
        foreach ($parser->getURLs() as $url => $tags) {
            $this->assertTrue(is_string($url));
            $this->assertTrue(is_array($tags));
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
    }

    /**
     * Generate test data
     * @return array
     */
    public
    function generateDataForTest()
    {
        return [
            [
                'https://www.xml-sitemaps.com/robots.txt',
            ]
        ];
    }
}
