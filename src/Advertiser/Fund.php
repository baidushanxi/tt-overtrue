<?php

namespace Sywzj\TTOvertrue\Advertiser;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class Ad
 * @package Sywzj\TTOvertrue\Ads
 * 广告计划相关
 */
class Fund extends ArrayCollection
{
    protected $access_token;

    const GET_URL = '/2/advertiser/fund/get/';
    const DAILY_STAT_URL = '/2/advertiser/fund/daily_stat/';
    const TRANSACTION_URL = '/2/advertiser/fund/transaction/get/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 获取广告主余额
     * @return ArrayCollection|mixed|string|null
     * @throws ErrorException
     */
    public function getFund($item)
    {
        $response = Http::httpGetJson(static::GET_URL,$item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response->get('data');
    }

    /**
     * 查询账号日流水
     * @return ArrayCollection|mixed|string|null
     * @throws ErrorException
     */
    public function dailyStat($item = [])
    {
        $response = Http::httpGetJson(static::DAILY_STAT_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 查询账号流水明细
     * @return ArrayCollection|mixed|string|null
     * @throws ErrorException
     */
    public function transaction($item = [])
    {
        $response = Http::httpGetJson(static::TRANSACTION_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }
}
