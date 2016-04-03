<?php
require_once(dirname(__FILE__) . "/../vendor/autoload.php");

use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

/**
 * Basic example
 */
try {
    $parser = new SitemapParser();
    $parser->parse('https://www.google.com/sitemap.xml');
    foreach ($parser->getURLs() as $url => $tags) {
        echo $url . '<br>';
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
