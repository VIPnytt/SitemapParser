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
        $this->assertIsArray($parser->getSitemaps());
        $this->assertIsArray($parser->getURLs());
        $this->assertTrue(count($parser->getSitemaps()) > 1);
        $this->assertTrue(count($parser->getURLs()) >= 1000);
        foreach ($parser->getSitemaps() as $parsedUrl => $tags) {
            $this->assertIsString($parsedUrl);
            $this->assertIsArray($tags);
            $this->assertTrue($parsedUrl === $tags['loc']);
            $this->assertNotFalse(filter_var($parsedUrl, FILTER_VALIDATE_URL));
        }
        foreach ($parser->getURLs() as $parsedUrl => $tags) {
            $this->assertIsString($parsedUrl);
            $this->assertIsArray($tags);
            $this->assertTrue($parsedUrl === $tags['loc']);
            $this->assertNotFalse(filter_var($parsedUrl, FILTER_VALIDATE_URL));
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
