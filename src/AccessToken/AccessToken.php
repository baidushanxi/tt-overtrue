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

    public $access_token = '';

    const ACCESS_TOKEN = '/oauth2/access_token/';
    const REFRESH_TOKEN = '/oauth2/refresh_token/';

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
        return $this->access_token
            ?: ($this->getOauthInfo()['access_token'] ?? '');
    }


    /**
     * 获取 AccessToken（不缓存，返回原始数据）.
     */
    public function refreshTokenResponse()
    {
        $query = [
            'grant_type' => 'refresh_token',
            'appid' => $this['app_id'],
            'secret' => $this['secret'],
            'refresh_token' => $this['refresh_token'],
        ];

        $response = Http::httpPostJson(static::REFRESH_TOKEN, $query)
            ->send();

        if ($response->containsKey('errcode')) {
            throw new \Exception($response['errmsg'], $response['errcode']);
        }

        return $response['data'];
    }


    /***
     * 获取Oauth认证所需信息
     * @return mixed
     * @throws \Exception
     */
    public function getOauthInfo()
    {
        $cacheId = $this->getCacheId();
        if ($this->cache && $data = $this->cache->fetch($cacheId)) {
            return $data;
        }

        $response = $this->refreshTokenResponse();

        if ($this->cache) {
            $this->cache->save($cacheId, $response, $response['expires_in']);
        }

        return $response;
    }

    /**
     * 手动设置Token
     * @param $token
     */
    public function setToken($token)
    {
        $this->access_token = $token;
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
        return sprintf('%s_access_token', $this['app_id']);
    }
}