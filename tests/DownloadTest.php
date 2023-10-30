<?php
namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class DownloadTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testDownload($url)
    {
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url);
        $this->assertIsArray($parser->getSitemaps());
        $this->assertIsArray($parser->getURLs());
        $this->assertTrue(count($parser->getSitemaps()) > 0 || count($parser->getURLs()) > 0);
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
                'https://www.google.com/sitemap.xml',
            ],
            [
                'https://php.net/sitemap.xml',
            ],
            [
                'https://www.yahoo.com/news/sitemaps/news-sitemap_index_US_en-US.xml.gz',
            ]
        ];
    }
}
