<?php


namespace Smartymoon\Crawler;


use Smartymoon\Crawler\Exceptions\CrawlerDetectedException;
use Smartymoon\Crawler\Exceptions\HttpStatusError;

class Client
{
    private $jar;
    private $client;
    private $checkCrawler;
    private $referer = '';

    /**
     * 不同页面有不同的防爬虫机制
     *
     * Client constructor.
     * @param callback|null $checkCrawler
     */
    public function __construct(callback $checkCrawler = null)
    {
        $this->checkCrawler = $checkCrawler;
        $this->jar = new \GuzzleHttp\Cookie\CookieJar;
        $this->client = new \GuzzleHttp\Client([
            'cookies' => true,
            [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36'
                ]
            ]
        ]);
    }

    public function get($url, $query = [], $options = []): string
    {
        $r = $this->client->get($url, $options  + ['cookies' => $this->jar] + ['query' => $query]);

        $statusCode = $r->getStatusCode();

        if ($statusCode != 200) {
            throw new HttpStatusError($url . '返回的结果是 ' . $statusCode);
        }

        $content =  $r->getBody()->getContents();

        if ($this->checkCrawler && call_user_func($this->checkCrawler, $content)) {
            throw new CrawlerDetectedException($url . '被检测为爬虫');
        }

        $this->referer = $url;

        // 每次爬数据假装浏览 10s
        sleep(10);

        return $content;
    }
}