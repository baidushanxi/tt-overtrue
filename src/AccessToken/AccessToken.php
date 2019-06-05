<?php

namespace Sywzj\TTOvertrue\AccessToken;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\Bridge\CacheTrait;
use Sywzj\TTOvertrue\Bridge\ErrorException;
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

    public $cache_prefix = 'jrtt';

    const ACCESS_TOKEN = '/oauth2/access_token/';
    const REFRESH_TOKEN = '/oauth2/refresh_token/';

    /**
     * 构造方法.
     */
    public function __construct($app_id, $secret, $auth_code = '')
    {
        $this->set('app_id', $app_id);
        $this->set('secret', $secret);
        $this->set('auth_code', $auth_code);
    }

    /**
     * 获取Token,过期刷新Token
     * @param bool $refresh
     * @return array
     * @throws ErrorException
     */
    public function getTokenString(bool $refresh = false): string
    {
        if($this->access_token) return $this->access_token;

        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey)) {
            $res = $cache->get($cacheKey);
            if($res['expires_time'] >= time()) {
                return $res['access_token'];
            }
        }
        $token = $this->refreshTokenResponse();
        return $token['access_token'];
    }

    /**
     * 授权认证
     * @return mixed
     * @throws ErrorException
     */
    public function oauth()
    {
        $query = [
            'grant_type' => 'auth_code',
            'app_id' => $this['app_id'],
            'secret' => $this['secret'],
            'auth_code' => $this['auth_code'],
        ];

        $response = Http::httpPostJson(static::ACCESS_TOKEN, $query)
            ->send();
        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $token = $response['data'];

        $this->setTokenCache($token['access_token'], $token['refresh_token'], time() + $token['expires_in']);
        return $token;
    }


    /**
     * 缓存Token
     * @param string $token
     * @param string $refresh_token
     * @param int    $lifetime
     *
     * @return \EasyWeChat\Kernel\Contracts\AccessTokenInterface
     *
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setTokenCache(string $token, string $refresh_token, int $expires_time)
    {
        $this->getCache()->set($this->getCacheKey(), [
            'access_token' => $token,
            'refresh_token' => $refresh_token,
            'expires_time' => $expires_time,
        ]);

        return $this;
    }


    /**
     * 设置缓存的Key
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf('%s_%s_access_token', $this->cache_prefix, $this['app_id']);
    }


    /**
     * 刷新Token
     */
    public function refreshTokenResponse()
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$cache->has($cacheKey)) {
            throw new ErrorException('please oauth account first', 500);
        }

        $token_cache = $cache->get($cacheKey);

        $query = [
            'grant_type' => 'refresh_token',
            'appid' => $this['app_id'],
            'secret' => $this['secret'],
            'refresh_token' => $token_cache['refresh_token'],
        ];

        $response = Http::httpPostJson(static::REFRESH_TOKEN, $query)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        $token = $response['data'];

        $this->setTokenCache($token['access_token'], $token['refresh_token'], time() + $token['expires_in']);

        return $response['data'];
    }

    /**
     * 手动设置Token
     * @param $token
     */
    public function setToken($token)
    {
        $this->access_token = $token;
    }
}