<?php
require_once(dirname(__FILE__) . "/../vendor/autoload.php");

use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

/**
 * Advanced recursive example
 * Full control in every step
 * Supports sitemaps of any number and size
 * Optimized to never run out of memory
 */

$config = [
    'guzzle' => [
        // put any GuzzleHttp options here
    ]
];

try {
    $parser = new SitemapParser('MyCustomUserAgent', $config);
    $parser->addToQueue(['https://www.google.com/robots.txt']);
    while (count($queue = $parser->getQueue()) > 0) {
        // Loop through each sitemap individually
        echo '<h3>Parsing sitemap: ' . $queue[0] . '</h3><hr>';
        $parser->parse($queue[0]);
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
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
