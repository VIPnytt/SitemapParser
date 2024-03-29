<?php

namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class RecursiveTest extends TestCase
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
        $this->assertIsArray($parser->getSitemaps());
        $this->assertIsArray($parser->getURLs());
        $this->assertTrue(count($parser->getSitemaps()) > 1 || count($parser->getURLs()) > 100);
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
                'https://edenapartmentsqueenanne.com/sitemap_index.xml',
                'https://livingnongmo.org/sitemap.xml',
                'https://loganwestom.com/sitemap_index.xml',
                'https://sawyerflats.com/sitemap.xml',
                'https://www.bellinghambaymarathon.org/sitemap_index.xml',
                'https://www.coachforteens.com/sitemap_index.xml',
                'https://www.hallerpostapts.com/sitemap_index.xml',
                'https://www.nongmoproject.org/sitemap.xml',
                'https://www.xml-sitemaps.com/robots.txt',
            ]
        ];
    }
}
