<?php
require_once(dirname(__FILE__) . "/../vendor/autoload.php");

use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

/**
 * Pre-downloaded sitemap example
 * No need to re-download if you already have it
 */
$body = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <sitemap>
    <loc>http://example.com/sitemap02.xml</loc>
  </sitemap>
  <sitemap>
    <loc>http://example.com/sitemap03.xml</loc>
  </sitemap>
  <url>
    <loc>http://example.com/</loc>
  </url>
  <url>
    <loc>http://example.com/about/</loc>
  </url>
</sitemapindex>
XML;

try {
    $parser = new SitemapParser();
    $parser->parse('http://example.com/sitemap.xml', $body);
    foreach ($parser->getSitemaps() as $url => $tags) {
        echo 'Sitemap: ' . $url . '<br>';
    }
    foreach ($parser->getURLs() as $url => $tags) {
        echo $url . '<br>';
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
