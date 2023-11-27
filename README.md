[![Build Status](https://travis-ci.org/VIPnytt/SitemapParser.svg?branch=master)](https://travis-ci.org/VIPnytt/SitemapParser)
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
- String parsing
- Custom User-Agent string
- Proxy support
- URL blacklist
- request throttling (using https://github.com/hamburgscleanest/guzzle-advanced-throttle)
- retry (using https://github.com/caseyamcl/guzzle_retry_middleware)
- advanced logging (using https://github.com/gmponos/guzzle_logger)

## Formats supported
- XML `.xml`
- Compressed XML `.xml.gz`
- Robots.txt rule sheet `robots.txt`
- Line separated text _(disabled by default)_

## Requirements:
- PHP [5.6 or 7.0+](http://php.net/supported-versions.php), alternatively [HHVM](http://hhvm.com)
- PHP extensions:
  - [mbstring](http://php.net/manual/en/book.mbstring.php)
  - [libxml](http://php.net/manual/en/book.libxml.php) _(enabled by default)_
  - [SimpleXML](http://php.net/manual/en/book.simplexml.php) _(enabled by default)_
- Optional:
  - https://github.com/caseyamcl/guzzle_retry_middleware
  - https://github.com/hamburgscleanest/guzzle-advanced-throttle
## Installation
The library is available for install via [Composer](https://getcomposer.org). Just add this to your `composer.json` file:
```json
{
    "require": {
        "vipnytt/sitemapparser": "^1.0"
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
    $parser->parse('http://php.net/sitemap.xml');
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
Parses any sitemap detected while parsing, to get an complete list of URLs.

Use `url_black_list` to skip sitemaps that are part of parent sitemap. Exact match only.
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
__Note:__ This is __disabled by default__ to avoid false positives when expecting XML, but fetches plain text instead.

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

### Throttling

1. Install middleware:
```bash
composer require hamburgscleanest/guzzle-advanced-throttle
```
2. Define host rules:

```php
$rules = new RequestLimitRuleset([
    'https://www.google.com' => [
        [
            'max_requests'     => 20,
            'request_interval' => 1
        ],
        [
            'max_requests'     => 100,
            'request_interval' => 120
        ]
    ]
]);
```
3. Create handler stack:

```php
$stack = new HandlerStack();
$stack->setHandler(new CurlHandler());
```
4. Create middleware:
```php
$throttle = new ThrottleMiddleware($rules);

 // Invoke the middleware
$stack->push($throttle());
 
// OR: alternatively call the handle method directly
$stack->push($throttle->handle());
```
5. Create client manually:
```php
$client = new \GuzzleHttp\Client(['handler' => $stack]);
```
6. Pass client as an argument or use `setClient` method:
```php
$parser = new SitemapParser();
$parser->setClient($client);
```
More details about this middle ware is available [here](https://github.com/hamburgscleanest/guzzle-advanced-throttle) 

### Automatic retry

1. Install middleware:
```bash
composer require caseyamcl/guzzle_retry_middleware
```

2. Create stack:
```php
$stack = new HandlerStack();
$stack->setHandler(new CurlHandler());
```

3. Add middleware to the stack:
```php
$stack->push(GuzzleRetryMiddleware::factory());
```

4. Create client manually:
```php
$client = new \GuzzleHttp\Client(['handler' => $stack]);
```

5. Pass client as an argument or use setClient method:
```php
$parser = new SitemapParser();
$parser->setClient($client);
```
More details about this middle ware is available [here](https://github.com/caseyamcl/guzzle_retry_middleware)

### Advanced logging

1. Install middleware:
```bash
composer require gmponos/guzzle_logger
```

2. Create PSR-3 style logger
```php
$logger = new Logger();
```

3. Create handler stack:

```php
$stack = new HandlerStack();
$stack->setHandler(new CurlHandler());
```

5. Push logger middleware to stack
```php
$stack->push(new LogMiddleware($logger));
```

6. Create client manually:
```php
$client = new \GuzzleHttp\Client(['handler' => $stack]);
```
7. Pass client as an argument or use `setClient` method:
```php
$parser = new SitemapParser();
$parser->setClient($client);
```
More details about this middleware config (like log levels, when to log and what to log) is available [here](https://github.com/gmponos/guzzle_logger)



### Additional examples
Even more examples available in the [examples](https://github.com/VIPnytt/SitemapParser/tree/master/examples) directory.

## Configuration
Available configuration options, with their default values:
```php
$config = [
    'strict' => true, // (bool) Disallow parsing of line-separated plain text
    'guzzle' => [
        // GuzzleHttp request options
        // http://docs.guzzlephp.org/en/latest/request-options.html
    ],
    // use this to ignore URL when parsing sitemaps that contain multiple other sitemaps. Exact match only.
    'url_black_list' => []
];
$parser = new SitemapParser('MyCustomUserAgent', $config);
```
_If an User-agent also is set using the GuzzleHttp request options, it receives the highest priority and replaces the other User-agent._
