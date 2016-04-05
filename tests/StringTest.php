<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     * @param string $body URL body content
     * @param array $result Test result to match
     */
    public function testString($url, $body, $result)
    {
        $parser = new SitemapParser('SitemapParser', ['strict' => false]);
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url, $body);
        $this->assertEquals($result['sitemaps'], $parser->getSitemaps());
        $this->assertEquals($result['urls'], $parser->getURLs());
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
                'http://www.example.com/sitemap.txt',
                <<<TEXT
http://www.example.com/sitemap1.xml
http://www.example.com/sitemap2.xml http://www.example.com/sitemap3.xml.gz
http://www.example.com/page1/
http://www.example.com/page2/ http://www.example.com/page3/file.gz
TEXT
                ,
                $result = [
                    'sitemaps' => [
                        'http://www.example.com/sitemap1.xml' => [
                            'loc' => 'http://www.example.com/sitemap1.xml',
                        ],
                        'http://www.example.com/sitemap2.xml' => [
                            'loc' => 'http://www.example.com/sitemap2.xml',
                        ],
                        'http://www.example.com/sitemap3.xml.gz' => [
                            'loc' => 'http://www.example.com/sitemap3.xml.gz',
                        ],
                    ],
                    'urls' => [
                        'http://www.example.com/page1/' => [
                            'loc' => 'http://www.example.com/page1/',
                        ],
                        'http://www.example.com/page2/' => [
                            'loc' => 'http://www.example.com/page2/',
                        ],
                        'http://www.example.com/page3/file.gz' => [
                            'loc' => 'http://www.example.com/page3/file.gz',
                        ],
                    ],
                ],
            ]
        ];
    }
}
