<?php

namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class RobotsTxtTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     * @param string $body URL body content
     * @param array $result Test result to match
     */
    public function testRobotsTxt($url, $body, $result)
    {
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url, $body);
        $this->assertEquals($result, $parser->getSitemaps());
        $this->assertEquals([], $parser->getURLs());
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'http://www.example.com/robots.txt',
                <<<ROBOTSTXT
    User-agent: *
    Disallow: /
    #Sitemap:http://www.example.com/sitemap.xml.gz
    Sitemap:http://www.example.com/sitemap.xml#comment
    ROBOTSTXT
                ,
                [
                    'http://www.example.com/sitemap.xml' => [
                        'loc' => 'http://www.example.com/sitemap.xml',
                        'lastmod' => null,
                    ],
                ],
            ],
            [
                'http://www.example.com/robots.txt',
                "# Alle Robots ausschlie" . chr(0xDF) . "en\n"
                . "User-agent: *\n"
                . "Sitemap: http://www.example.com/sitemap.xml\n",
                [
                    'http://www.example.com/sitemap.xml' => [
                        'loc' => 'http://www.example.com/sitemap.xml',
                        'lastmod' => null,
                    ],
                ],
            ],
        ];
    }
}
