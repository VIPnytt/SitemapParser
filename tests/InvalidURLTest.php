<?php
namespace vipnytt\SitemapParser\Tests;

use vipnytt\SitemapParser;

class InvalidURLTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider generateDataForTest
     * @param string $url URL
     */
    public function testInvalidURL($url)
    {
        $this->expectException('\vipnytt\SitemapParser\Exceptions\SitemapParserException');
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);
        $parser->parse($url);
    }

    /**
     * Generate test data
     * @return array
     */
    public function generateDataForTest()
    {
        return [
            [
                'htt://www.example.c/',
            ],
            [
                'http:/www.example.com/',
            ],
            [
                'https//www.example.com/',
            ]
        ];
    }
}
