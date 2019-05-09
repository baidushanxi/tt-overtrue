<?php

namespace Sywzj\TTOvertrue\AccessToken;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\Bridge\CacheTrait;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class AccessToken
 * @package Sywzj\TTOvertrue\AccessToken
 * token相关操作
 */
class AccessToken extends ArrayCollection
{
    /*
     * Cache Trait
     * */
    use CacheTrait;

    const ACCESS_TOKEN = 'https://ad.toutiao.com/open_api/oauth2/access_token/';

    /**
     * 构造方法.
     */
    public function __construct($app_id, $secret)
    {
        $this->set('app_id', $app_id);
        $this->set('secret', $secret);
    }

    /**
     * 获取 AccessToken（调用缓存，返回 String）.
     */
    public function getTokenString()
    {
        return "7dc4079d9253f7e8f301566693cafde350255e0d";
        $cacheId = $this->getCacheId();

        if ($this->cache && $data = $this->cache->fetch($cacheId)) {
            return $data['access_token'];
        }

        $response = $this->getTokenResponse();

        if ($this->cache) {
            $this->cache->save($cacheId, $response, $response['expires_in']);
        }

        return $response['access_token'];
    }

    /**
     * 获取 AccessToken（不缓存，返回原始数据）.
     */
    public function getTokenResponse()
    {
        $query = [
            'grant_type' => 'auth_code',
            'appid' => $this['app_id'],
            'secret' => $this['secret'],
        ];

        $response = Http::request('POST', static::ACCESS_TOKEN)
            ->withQuery($query)
            ->send();

        if ($response->containsKey('errcode')) {
            throw new \Exception($response['errmsg'], $response['errcode']);
        }

        return $response;
    }

    /**
     * 从缓存中清除.
     */
    public function clearFromCache()
    {
        return $this->cache
            ? $this->cache->delete($this->getCacheId())
            : false;
    }

    /**
     * 获取缓存 ID.
     */
    public function getCacheId()
    {
        return sprintf('%s_access_token', $this['appid']);
    }
}