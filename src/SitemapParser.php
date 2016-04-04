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
    protected $currentURL = null;

    /**
     * Constructor
     *
     * @param string $userAgent User-Agent to send with every HTTP(S) request
     * @param array $config Configuration options
     * @throws SitemapParserException
     */
    public function __construct($userAgent = 'SitemapParser', $config = [])
    {
        if (!extension_loaded('simplexml')) {
            throw new SitemapParserException('The extension `simplexml` must be installed and loaded for this library');
        }
        if (!extension_loaded('mbstring')) {
            throw new SitemapParserException('The extension `mbstring` must be installed and loaded for this library');
        }
        mb_language("uni");
        if (!mb_internal_encoding('UTF-8')) {
            throw new SitemapParserException('Unable to set internal character encoding to UTF-8');
        }
        $this->config = $config;
        $this->userAgent = $userAgent;
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
    public function addToQueue($urlArray)
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
        if (parse_url($this->currentURL, PHP_URL_PATH) == '/robots.txt') {
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
        if (isset($sitemapJson->sitemap)) {
            $this->parseJson('sitemap', $sitemapJson->sitemap);
        }
        if (isset($sitemapJson->url)) {
            $this->parseJson('url', $sitemapJson->url);
        }
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
            throw new SitemapParserException('Passed URL not valid according to filter_var function');
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
     * @return void
     */
    protected function parseRobotstxt($robotstxt)
    {
        preg_match_all('#Sitemap:*(.*)#', $robotstxt, $match);
        if (isset($match[1])) {
            foreach ($match[1] as $sitemap) {
                $sitemap = trim($sitemap);
                $this->addArray('sitemap', ['loc' => $sitemap]);
            }
        }
    }

    /**
     * Validate URL arrays and add them to their corresponding arrays
     *
     * @param string $type sitemap|url
     * @param array $array Tag array
     * @return bool
     */
    protected function addArray($type, $array)
    {
        if (isset($array['loc']) && filter_var($array['loc'], FILTER_VALIDATE_URL) !== false) {
            switch ($type) {
                case 'sitemap':
                    $this->sitemaps[$array['loc']] = $array;
                    return true;
                case 'url':
                    $this->urls[$array['loc']] = $array;
                    return true;
            }
        }
        return false;
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
     * Parse plain text
     *
     * @param string $string
     * @return void
     */
    protected function parseString($string)
    {
        $offset = 0;
        while (preg_match('/(\S+)/', $string, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $offset = $match[0][1] + strlen($match[0][0]);
            if (filter_var($match[0][0], FILTER_VALIDATE_URL) !== false) {
                if ($this->isSitemapURL($match[0][0])) {
                    $this->addArray('sitemap', ['loc' => $match[0][0]]);
                    continue;
                }
                $this->addArray('url', ['loc' => $match[0][0]]);
            }
        }
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
            substr($path, -4) === ".xml" ||
            substr($path, -7) === '.xml.gz'
        );
    }

    /**
     * Parse Json object
     *
     * @param string $type Sitemap or URL
     * @param \SimpleXMLElement $json object
     * @return void
     */
    protected function parseJson($type, $json)
    {
        foreach ($json as $url) {
            $this->addArray($type, (array)$url);
        }
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
