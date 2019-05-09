<?php

namespace Sywzj\TTOvertrue\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class Campaign
 * @package Sywzj\TTOvertrue\Report
 * 广告组报表
 */
class Campaign extends ArrayCollection
{
    protected $access_token;

    const GROUP_REPORT_URL = '/2/report/campaign/get/';//广告组报表
    const CENTRAL_REPORT_URL = '/2/report/advertiser/get/';//广告主报表

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 获取广告组报表信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function groupReport($item)
    {

        $item['page'] = empty($item['page']) ? 1 : $item['page'];
        $item['page_size'] = empty($item['page_size']) ? 100 : $item['page_size'];
        $item['time_granularity'] = empty($item['time_granularity']) ? 'STAT_TIME_GRANULARITY_DAILY' : $item['time_granularity'];
        $item['group_by'] = empty($item['group_by']) ? ['STAT_GROUP_BY_FIELD_STAT_TIME'] : $item['group_by'];
        if (!empty($item['filtering'])) {
            $item['filtering'] = json_encode($item['filtering']);
        }

        $response = Http::httpGetJson(static::GROUP_REPORT_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }

    /**
     * 获取广告主报表信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function centralReport($item)
    {
        $item['page'] = empty($item['page']) ? 1 : $item['page'];
        $item['page_size'] = empty($item['page_size']) ? 100 : $item['page_size'];
        $item['time_granularity'] = empty($item['time_granularity']) ? 'STAT_TIME_GRANULARITY_DAILY' : $item['time_granularity'];

        $response = Http::httpGetJson(static::CENTRAL_REPORT_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }

}