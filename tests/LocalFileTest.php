<?php

namespace vipnytt\SitemapParser\Tests;

use PHPUnit\Framework\TestCase;
use vipnytt\SitemapParser;

class RecursiveTest extends TestCase {

    public function testLocalFileXMLFile()
    {
        $parser = new SitemapParser('SitemapParser');
        $this->assertInstanceOf('vipnytt\SitemapParser', $parser);

        $tmpfname = tempnam(sys_get_temp_dir(), "sitemap_parser_test_file");
        $fileContent = <<<XMLSITEMAP
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>http://www.example.com/sitemap.xml</loc>
    <lastmod>2004-10-01T18:23:17+00:00</lastmod>
  </sitemap>
</sitemapindex>
XMLSITEMAP;
        file_put_contents($tmpfname, $fileContent);
        $parser->parse('file:///'.$tmpfname);
        $this->assertEquals([
            'http://www.example.com/sitemap.xml' => [
                'loc' => 'http://www.example.com/sitemap.xml',
                'lastmod' => '2004-10-01T18:23:17+00:00',
                'namespaces' => [],
            ],
        ], $parser->getSitemaps());
    }

}
