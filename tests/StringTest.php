<?php

namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class StringTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testString($url)
    {
        $parser = new SitemapParser('SitemapParser', ['strict' => false]);
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url);
        $this->assertInternalType('array', $parser->getSitemaps());
        $this->assertInternalType('array', $parser->getURLs());
        $this->assertTrue(count($parser->getSitemaps()) > 1);
        $this->assertTrue(count($parser->getURLs()) >= 1000);
        foreach ($parser->getSitemaps() as $url => $tags) {
            $this->assertInternalType('string', $url);
            $this->assertInternalType('array', $tags);
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
        foreach ($parser->getURLs() as $url => $tags) {
            $this->assertInternalType('string', $url);
            $this->assertInternalType('array', $tags);
            $this->assertTrue($url === $tags['loc']);
            $this->assertNotFalse(filter_var($url, FILTER_VALIDATE_URL));
        }
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'https://www.xml-sitemaps.com/urllist.txt',
            ]
        ];
    }
}
