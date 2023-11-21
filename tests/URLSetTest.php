<?php

namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class URLSetTest extends TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     * @param string $body URL body content
     * @param array $result Test result to match
     */
    public function testURLSet($url, $body, $result)
    {
        $parser = new SitemapParser('SitemapParser');
        self::assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url, $body);
        self::assertEquals([], $parser->getSitemaps());
        self::assertEquals($result, $parser->getURLs());
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'http://www.example.com/sitemap.xml',

                <<<XMLSITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
   <url>
      <loc>http://www.example.com/</loc>
      <lastmod>2005-01-01</lastmod>
      <changefreq>monthly</changefreq>
      <priority>0.8</priority>
      <xhtml:link rel="alternate" hreflang="en-US" href="http://www.example.com/?land=en&amp;country=US" />
      <xhtml:link rel="alternate" hreflang="en-GB" href="http://www.example.com/?land=en&amp;country=GB" />
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=12&amp;desc=vacation_hawaii</loc>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=73&amp;desc=vacation_new_zealand</loc>
      <lastmod>2004-12-23</lastmod>
      <changefreq>weekly</changefreq>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=74&amp;desc=vacation_newfoundland</loc>
      <lastmod>2004-12-23T18:00:15+00:00</lastmod>
      <priority>0.3</priority>
   </url>
   <url>
      <loc>http://www.example.com/catalog?item=83&amp;desc=vacation_usa</loc>
      <lastmod>2004-11-23</lastmod>
   </url>
</urlset>
XMLSITEMAP
                ,
                [
                    'http://www.example.com/' => [
                        'loc' => 'http://www.example.com/',
                        'lastmod' => '2005-01-01',
                        'changefreq' => 'monthly',
                        'priority' => '0.8',
                        'namespaces'=> [
                            'xhtml' => [
                                [
                                    'link' => [
                                        'rel' => 'alternate',
                                        'hreflang' => 'en-US',
                                        'href' => 'http://www.example.com/?land=en&country=US'
                                    ]
                                ],
                                [
                                    'link' => [
                                        'rel' => 'alternate',
                                        'hreflang' => 'en-GB',
                                        'href' => 'http://www.example.com/?land=en&country=GB'
                                    ]
                                ],
                            ],
                        ],
                    ],
                    'http://www.example.com/catalog?item=12&desc=vacation_hawaii' => [
                        'loc' => 'http://www.example.com/catalog?item=12&desc=vacation_hawaii',
                        'changefreq' => 'weekly',
                        'lastmod' => null,
                        'priority' => null,
                        'namespaces'=> [
                            'xhtml' => [],
                        ],
                    ],
                    'http://www.example.com/catalog?item=73&desc=vacation_new_zealand' => [
                        'loc' => 'http://www.example.com/catalog?item=73&desc=vacation_new_zealand',
                        'lastmod' => '2004-12-23',
                        'changefreq' => 'weekly',
                        'priority' => null,
                        'namespaces'=> [
                            'xhtml' => [],
                        ],
                    ],
                    'http://www.example.com/catalog?item=74&desc=vacation_newfoundland' => [
                        'loc' => 'http://www.example.com/catalog?item=74&desc=vacation_newfoundland',
                        'lastmod' => '2004-12-23T18:00:15+00:00',
                        'priority' => '0.3',
                        'changefreq' => null,
                        'namespaces'=> [
                            'xhtml' => [],
                        ],
                    ],
                    'http://www.example.com/catalog?item=83&desc=vacation_usa' => [
                        'loc' => 'http://www.example.com/catalog?item=83&desc=vacation_usa',
                        'lastmod' => '2004-11-23',
                        'changefreq' => null,
                        'priority' => null,
                        'namespaces'=> [
                            'xhtml' => [],
                        ],
                    ],
                ]
            ]
        ];
    }
}
