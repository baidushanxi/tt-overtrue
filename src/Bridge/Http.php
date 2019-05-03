<?php

namespace Sywzj\TTOvertrue\Bridge;

use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;
use Sywzj\TTOvertrue\AccessToken\AccessToken;

class Http
{
    /**
     * Request Url.
     */
//    protected $uri = "https://ad.toutiao.com/open_api";

    protected $uri = "https://test-ad.toutiao.com/open_api";


    /**
     * Request $instance.
     */
    protected static $instance;

    /**
     * Request Method.
     */
    protected $method;

    /**
     * Request json.
     */
    protected $json;

    /**
     * Request Query.
     */
    protected $query = [];

    /**
     * Request Headers.
     */
    protected $headers = [];

    /**
     * Query With AccessToken.
     */
    protected $accessToken;

    /**
     * initialize.
     */
    public function __construct(string $method = 'GET' ,string $uri,array $json = [])
    {
        $this->uri = $this->uri . $uri;
        $this->method = $method;
        $this->json = $json;
    }

    /**
     * 静态工厂方法，返还此类的唯一实例
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new Client();
        }
        return self::$instance;
    }

    /**
     * Create Client Factory.
     */
    public static function httpPostJson($uri, array $options = [])
    {
        return new static('POST', $uri, $options);
    }

    /**
     * Create Client Factory.
     */
    public static function httpGetJson($uri, array $options = [])
    {
        return new static('GET',$uri, $options);
    }

    /**
     * Request Query.
     */
    public function withQuery(array $query)
    {
        $this->query = array_merge($this->query, $query);

        return $this;
    }

    /**
     * Query With AccessToken.
     */
    public function withAccessToken(AccessToken $accessToken)
    {
        $this->headers = ['Access-Token' => $accessToken->getTokenString()];

        return $this;
    }

    /**
     * Send Request.
     */
    public function send($asArray = true)
    {
        $options = [];

        // query
        if (!empty($this->query)) {
            $options['query'] = $this->query;
        }

        // json
        if (!empty($this->json)) {
            $options['json'] = $this->json;
        }

        // json
        if (!empty($this->json)) {
            $options['headers'] = $this->headers;
        }

        $client = self::getInstance();
        $response = $client->request($this->method, $this->uri, $options);
//        $http_code = $response->getStatusCode();
        $contents = $response->getBody()->getContents();

        if (!$asArray) {
            return $contents;
        }

        $array = Serializer::parse($contents);

        return new ArrayCollection($array);
    }
}
