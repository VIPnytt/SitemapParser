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
        $this->assertInternalType('array', $parser->getSitemaps());
        $this->assertInternalType('array', $parser->getURLs());
        $this->assertTrue(count($parser->getSitemaps()) > 0 || count($parser->getURLs()) > 0);
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
                'http://www.google.com/sitemap.xml',
            ],
            [
                'http://php.net/sitemap.xml',
            ],
            [
                'https://www.yahoo.com/news/sitemaps/news-sitemap_index_US_en-US.xml.gz',
            ]
        ];
    }
}
