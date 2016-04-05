<?php
namespace vipnytt;

use GuzzleHttp;
use SimpleXMLElement;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;

/**
 * SitemapParser class
 *
 * @license https://opensource.org/licenses/MIT MIT license
 * @link https://github.com/VIPnytt/SitemapParser
 *
 * Specifications:
 * @link http://www.sitemaps.org/protocol.html
 */
class SitemapParser
{
    /**
     * Default encoding
     */
    const ENCODING = 'UTF-8';

    /**
     * XML file extension
     */
    const XML_EXTENSION = '.xml';

    /**
     * Compressed XML file extension
     */
    const XML_EXTENSION_COMPRESSED = '.xml.gz';

    /**
     * XML Sitemap tag
     */
    const XML_TAG_SITEMAP = 'sitemap';

    /**
     * XML URL tag
     */
    const XML_TAG_URL = 'url';

    /**
     * Robots.txt path
     */
    const ROBOTSTXT_PATH = '/robots.txt';

    /**
     * Robots.txt sitemap prefix
     */
    const ROBOTSTXT_PREFIX = 'Sitemap:';

    /**
     * User-Agent to send with every HTTP(S) request
     * @var string
     */
    protected $userAgent;

    /**
     * Configuration options
     * @var array
     */
    protected $config = [];

    /**
     * Sitemaps discovered
     * @var array
     */
    protected $sitemaps = [];

    /**
     * URLs discovered
     * @var array
     */
    protected $urls = [];

    /**
     * Sitemap URLs discovered but not yet parsed
     * @var array
     */
    protected $queue = [];

    /**
     * Parsed URLs history
     * @var array
     */
    protected $history = [];

    /**
     * Current URL being parsed
     * @var null|string
     */
    protected $currentURL;

    /**
     * Constructor
     *
     * @param string $userAgent User-Agent to send with every HTTP(S) request
     * @param array $config Configuration options
     * @throws SitemapParserException
     */
    public function __construct($userAgent = 'SitemapParser', array $config = [])
    {
        if (!extension_loaded('simplexml')) {
            throw new SitemapParserException('The extension `simplexml` must be installed and loaded for this library');
        }
        if (!extension_loaded('mbstring')) {
            throw new SitemapParserException('The extension `mbstring` must be installed and loaded for this library');
        }
        mb_language("uni");
        if (!mb_internal_encoding(self::ENCODING)) {
            throw new SitemapParserException('Unable to set internal character encoding to `' . self::ENCODING . '`');
        }
        $this->userAgent = $userAgent;
        $this->config = $config;
    }

    /**
     * Parse Recursive
     *
     * @param string $url
     * @return void
     * @throws SitemapParserException
     */
    public function parseRecursive($url)
    {
        $this->addToQueue([$url]);
        while (count($todo = $this->getQueue()) > 0) {
            $sitemaps = $this->sitemaps;
            $urls = $this->urls;
            $this->parse($todo[0]);
            $this->sitemaps = array_merge_recursive($sitemaps, $this->sitemaps);
            $this->urls = array_merge_recursive($urls, $this->urls);
        }
    }

    /**
     * Add an array of URLs to the parser queue
     *
     * @param array $urlArray
     */
    public function addToQueue(array $urlArray)
    {
        foreach ($urlArray as $url) {
            $this->queue[] = $url;
        }
    }

    /**
     * Sitemap URLs discovered but not yet parsed
     *
     * @return array
     */
    public function getQueue()
    {
        $this->queue = array_values(array_diff(array_unique(array_merge($this->queue, array_keys($this->sitemaps))), $this->history));
        return $this->queue;
    }

    /**
     * Parse
     *
     * @param string $url URL to parse
     * @param string|null $urlContent URL body content (skip download)
     * @return void
     * @throws SitemapParserException
     */
    public function parse($url, $urlContent = null)
    {
        $this->clean();
        $this->currentURL = $url;
        $response = (is_string($urlContent)) ? $urlContent : $this->getContent();
        $this->history[] = $this->currentURL;
        if (parse_url($this->currentURL, PHP_URL_PATH) === self::ROBOTSTXT_PATH) {
            $this->parseRobotstxt($response);
            return;
        }
        // Check if content is an gzip file
        if (mb_strpos($response, "\x1f\x8b\x08", 0, "US-ASCII") === 0) {
            $response = gzdecode($response);
        }
        $sitemapJson = $this->generateXMLObject($response);
        if ($sitemapJson instanceof SimpleXMLElement === false) {
            $this->parseString($response);
            return;
        }
        $this->parseJson(self::XML_TAG_SITEMAP, $sitemapJson);
        $this->parseJson(self::XML_TAG_URL, $sitemapJson);
    }

    /**
     * Cleanup between each parse
     *
     * @return void
     */
    protected function clean()
    {
        $this->sitemaps = [];
        $this->urls = [];
    }

    /**
     * Request the body content of an URL
     *
     * @return string Raw body content
     * @throws SitemapParserException
     */
    protected function getContent()
    {
        if (!filter_var($this->currentURL, FILTER_VALIDATE_URL)) {
            throw new SitemapParserException('Passed URL not valid according to the filter_var function');
        }
        try {
            if (!isset($this->config['guzzle']['headers']['User-Agent'])) {
                $this->config['guzzle']['headers']['User-Agent'] = $this->userAgent;
            }
            $client = new GuzzleHttp\Client();
            $res = $client->request('GET', $this->currentURL, $this->config['guzzle']);
            return $res->getBody();
        } catch (GuzzleHttp\Exception\TransferException $e) {
            throw new SitemapParserException($e->getMessage());
        }
    }

    /**
     * Search for sitemaps in the robots.txt content
     *
     * @param string $robotstxt
     * @return bool
     */
    protected function parseRobotstxt($robotstxt)
    {
        $array = array_map('trim', preg_split('/\R/', $robotstxt));
        foreach ($array as $line) {
            if (mb_stripos($line, self::ROBOTSTXT_PREFIX) === 0) {
                $url = mb_substr($line, mb_strlen(self::ROBOTSTXT_PREFIX));
                if (($pos = mb_stripos($url, '#')) !== false) {
                    $url = mb_substr($url, 0, $pos);
                }
                $url = preg_split('/\s+/', trim($url))[0];
                $this->addArray('sitemap', ['loc' => $url]);
            }
        }
        return true;
    }

    /**
     * Validate URL arrays and add them to their corresponding arrays
     *
     * @param string $type sitemap|url
     * @param array $array Tag array
     * @return bool
     */
    protected function addArray($type, array $array)
    {
        if (isset($array['loc']) && filter_var($array['loc'], FILTER_VALIDATE_URL) !== false) {
            switch ($type) {
                case self::XML_TAG_SITEMAP:
                    $tags = [
                        'lastmod',
                        'changefreq',
                        'priority',
                    ];
                    $this->sitemaps[$array['loc']] = $this->fixMissingTags($tags, $array);
                    return true;
                case self::XML_TAG_URL:
                    $tags = [
                        'lastmod',
                    ];
                    $this->urls[$array['loc']] = $this->fixMissingTags($tags, $array);
                    return true;
            }
        }
        return false;
    }

    /**
     * Check for missing values and set them to null
     *
     * @param array $tags Tags check if exists
     * @param array $array Array to check
     * @return array
     */
    protected function fixMissingTags(array $tags, array $array)
    {
        foreach ($tags as $tag) {
            if (empty($array)) {
                $array[$tag] = null;
            }
        }
        return $array;
    }

    /**
     * Generate the \SimpleXMLElement object if the XML is valid
     *
     * @param string $xml
     * @return \SimpleXMLElement|false
     */
    protected function generateXMLObject($xml)
    {
        try {
            libxml_use_internal_errors(true);
            return new SimpleXMLElement($xml, LIBXML_NOCDATA);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Parse line separated text string
     *
     * @param string $string
     * @return bool
     */
    protected function parseString($string)
    {
        if (!isset($this->config['strict']) || $this->config['strict'] !== false) {
            // Strings are not part of any documented sitemap standard
            return false;
        }
        $array = array_map('trim', preg_split('/\R/', $string));
        foreach ($array as $line) {
            if ($this->isSitemapURL($line)) {
                $this->addArray(self::XML_TAG_SITEMAP, ['loc' => $line]);
                continue;
            }
            $this->addArray(self::XML_TAG_URL, ['loc' => $line]);
        }
        return true;
    }

    /**
     * Check if the URL may contain an Sitemap
     *
     * @param string $url
     * @return bool
     */
    protected function isSitemapURL($url)
    {
        $path = parse_url($url, PHP_URL_PATH);
        return filter_var($url, FILTER_VALIDATE_URL) !== false && (
            substr($path, -strlen(self::XML_EXTENSION)) === self::XML_EXTENSION ||
            substr($path, -strlen(self::XML_EXTENSION_COMPRESSED)) === self::XML_EXTENSION_COMPRESSED
        );
    }

    /**
     * Parse Json object
     *
     * @param string $type Sitemap or URL
     * @param \SimpleXMLElement $json object
     * @return bool
     */
    protected function parseJson($type, \SimpleXMLElement $json)
    {
        if (!isset($json->$type)) {
            return false;
        }
        foreach ($json->$type as $url) {
            $this->addArray($type, (array)$url);
        }
        return true;
    }

    /**
     * Sitemaps discovered
     *
     * @return array
     */
    public function getSitemaps()
    {
        return $this->sitemaps;
    }

    /**
     * URLs discovered
     *
     * @return array
     */
    public function getURLs()
    {
        return $this->urls;
    }
}
