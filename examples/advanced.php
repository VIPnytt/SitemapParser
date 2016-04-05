<?php
require_once(dirname(__FILE__) . "/../vendor/autoload.php");

use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

/**
 * Advanced example
 */

$config = [
    'guzzle' => [
        // put any GuzzleHttp options here
    ]
];

try {
    $parser = new SitemapParser('MyCustomUserAgent', $config);
    $parser->parse('https://www.google.com/robots.txt');
    foreach ($parser->getSitemaps() as $url => $tags) {
        echo 'Sitemap<br>';
        echo 'URL: ' . $url . '<br>';
        echo 'LastMod: ' . $tags['lastmod'] . '<br>';
        echo '<hr>';
    }
    foreach ($parser->getURLs() as $url => $tags) {
        echo 'URL: ' . $url . '<br>';
        echo 'LastMod: ' . $tags['lastmod'] . '<br>';
        echo 'ChangeFreq: ' . $tags['changefreq'] . '<br>';
        echo 'Priority: ' . $tags['priority'] . '<br>';
        echo '<hr>';
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
