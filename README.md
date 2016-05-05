[![Build Status](https://travis-ci.org/VIPnytt/SitemapParser.svg?branch=master)](https://travis-ci.org/VIPnytt/X-Robots-Tag-parser)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/VIPnytt/SitemapParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/VIPnytt/SitemapParser/?branch=master)
[![Code Climate](https://codeclimate.com/github/VIPnytt/SitemapParser/badges/gpa.svg)](https://codeclimate.com/github/VIPnytt/SitemapParser)
[![Test Coverage](https://codeclimate.com/github/VIPnytt/SitemapParser/badges/coverage.svg)](https://codeclimate.com/github/VIPnytt/SitemapParser/coverage)
[![License](https://poser.pugx.org/VIPnytt/SitemapParser/license)](https://github.com/VIPnytt/SitemapParser/blob/master/LICENSE)
[![Packagist](https://img.shields.io/packagist/v/VIPnytt/SitemapParser.svg)](https://packagist.org/packages/VIPnytt/SitemapParser)
[![Join the chat at https://gitter.im/VIPnytt/SitemapParser](https://badges.gitter.im/VIPnytt/SitemapParser.svg)](https://gitter.im/VIPnytt/SitemapParser)

# XML Sitemap parser
An easy-to-use PHP library to parse XML Sitemaps compliant with the [Sitemaps.org protocol](http://www.sitemaps.org/protocol.html).

The [Sitemaps.org](http://www.sitemaps.org/) protocol is the leading standard and is supported by Google, Bing, Yahoo, Ask and many others.

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/2d3fbd49-66c4-4ab9-9007-aaeec6956d30/big.png)](https://insight.sensiolabs.com/projects/2d3fbd49-66c4-4ab9-9007-aaeec6956d30)

## Features
- Basic parsing
- Recursive parsing
- Custom User-Agent string
- Proxy support
- Offline parsing

## Formats supported
- XML `.xml`
- Compressed XML `.xml.gz`
- Robots.txt rule sheet `robots.txt`
- Line separated text _[disabled by default]_

## Requirements:
- PHP [>=5.6](http://php.net/supported-versions.php)
- PHP [mbstring](http://php.net/manual/en/book.mbstring.php) extension
- PHP [libxml](http://php.net/manual/en/book.libxml.php) extension _[enabled by default]_
- PHP [SimpleXML](http://php.net/manual/en/book.simplexml.php) extension _[enabled by default]_

## Installation
The library is available for install via [Composer](https://getcomposer.org). Just add this to your `composer.json` file:
```json
{
    "require": {
        "vipnytt/sitemapparser": "1.0.*"
    }
}
```
Then run `composer update`.

## Getting Started

### Basic example
Returns an list of URLs only.
```php
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

try {
    $parser = new SitemapParser();
    $parser->parse('https://www.google.com/sitemap.xml');
    foreach ($parser->getURLs() as $url => $tags) {
        echo $url . '<br>';
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
```

### Advanced
Returns all available tags, for both Sitemaps and URLs.
```php
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

try {
    $parser = new SitemapParser('MyCustomUserAgent');
    $parser->parse('http://php.net/sitemap.xml');
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
```

### Recursive
Parses any sitemap detected while parsing, to get an complete list of URLs
```php
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

try {
    $parser = new SitemapParser('MyCustomUserAgent');
    $parser->parseRecursive('http://www.google.com/robots.txt');
    echo '<h2>Sitemaps</h2>';
    foreach ($parser->getSitemaps() as $url => $tags) {
        echo 'URL: ' . $url . '<br>';
        echo 'LastMod: ' . $tags['lastmod'] . '<br>';
        echo '<hr>';
    }
    echo '<h2>URLs</h2>';
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
```

### Parsing of line separated text strings
__Note: This is disabled by default__ to avoid false positives when expecting XML, but get some plain text in return.

To disable `strict` standards, simply pass this configuration to constructor parameter #2: ````['strict' => false]````.
```php
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

try {
    $parser = new SitemapParser('MyCustomUserAgent', ['strict' => false]);
    $parser->parse('https://www.xml-sitemaps.com/urllist.txt');
    foreach ($parser->getSitemaps() as $url => $tags) {
            echo $url . '<br>';
    }
    foreach ($parser->getURLs() as $url => $tags) {
            echo $url . '<br>';
    }
} catch (SitemapParserException $e) {
    echo $e->getMessage();
}
```

### Additional examples
Even more examples available in the [examples](https://github.com/VIPnytt/SitemapParser/tree/master/examples) directory.
