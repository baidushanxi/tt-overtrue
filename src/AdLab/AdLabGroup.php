<?php
namespace Sywzj\TTOvertrue\AdLab;

use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\Http;

class AdLabGroup
{
    const CREATE_URL = '/2/adlab/group/create/';
    const GET_URL = '/2/adlab/group/get/';
    const CONVERT_URL = '/2/adlab/group_convert/update/';
    const AUDIENCE_URL = '/2/adlab/group_audience/update/';
    const CREATIVE_URL = '/2/adlab/group_creative/update/';
    const BUDGET_URL = '/2/adlab/group_budget/update/';
    const GROUP_STATUS_URL = '/2/adlab/group_status/update/';
    const AD_STATUS_URL = '/2/adlab/ad_status/update/';
    const GROUP_AD_URL = '/2/adlab/group_ads/get/';
    const GROUP_MATERIAL_URL = '/2/adlab/group_material_info/get/';
    const AD_BID_URL = '/2/adlab/ad_bid/update/';
    const ADS_BUDGET_URL = '/2/adlab/ads_budget/append/';
    const CANCEL_AD_BUDGET_URL = '/2/adlab/ad_budget/cancel/';
    const AD_BUDGET_GET_URL = '/2/adlab/budget/get/';

    const AD_TYPE_ANDROID = 'ANDROID';
    const AD_TYPE_EXTERNAL = 'EXTERNAL';
    const AD_TYPE_GOODS = 'GOODS';
    const AD_TYPE_GOODS_V2 = 'GOODS_V2';
    const AD_TYPE_IOS = 'IOS';
    const AD_TYPE_LINK = 'LINK';

    const STATUS_DELETED = 'DELETED';
    const STATUS_DISABLED = 'DISABLED';
    const STATUS_ENABLED = 'ENABLED';

    const SCENARIO_COMMON = 'COMMON';
    const SCENARIO_GAME = 'GAME';
    const SCENARIO_GAME_UNION = 'GAME_UNION';
    const SCENARIO_INTERNET_SERVICE = 'INTERNET_SERVICE';
    const SCENARIO_IT_SERVER_UNION = 'IT_SERVER_UNION';

    protected $access_token;

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * 创投放关键
     *
     * @param int|string $advertiser_id
     * @param string $name 不超过100字符
     * @param string $ad_type
     * @param string $scenario
     * @return int
     * @throws ErrorException
     */
    public function create($advertiser_id, $name, $ad_type, $scenario)
    {
        $item = compact('advertiser_id', 'name', 'ad_type', 'scenario');
        $response = Http::httpPostJson(static::CREATE_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['group_id'];
    }

    /**
     * 获取投放管家项目
     *
     * @param $advertiser_id
     * @param $group_id
     * @return array
     * @throws ErrorException
     */
    public function getGroup($advertiser_id, $group_id)
    {
        $params = compact('advertiser_id', 'group_id');

        $response = Http::httpGetJson(static::GET_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }

    /**
     * 设置投放管家定向
     *
     * @param $advertiser_id
     * @param $group_id
     * @param array $params
     * @return int
     * @throws ErrorException
     * https://ad.oceanengine.com/openapi/doc/index.html?id=1692
     */
    public function setAudience($advertiser_id, $group_id, array $params)
    {
        return $this->setGroup(self::AUDIENCE_URL, $advertiser_id, $group_id, $params);
    }

    /**
     * 设置项目转化信息
     *
     * @param $advertiser_id
     * @param $group_id
     * @param array $params [convert_id, download_mode, package, platform, url, web_url]
     * @throws ErrorException
     * @return int
     */
    public function setConvert($advertiser_id, $group_id, array $params)
    {
        return $this->setGroup(self::CONVERT_URL, $advertiser_id, $group_id, $params);
    }

    /**
     * 设置项目创意信息
     * https://ad.oceanengine.com/openapi/doc/index.html?id=1693
     *
     * @param $advertiser_id
     * @param $group_id
     * @param array $params
     * @return int
     * @throws ErrorException
     */
    public function setCreative($advertiser_id, $group_id, array $params)
    {
        return $this->setGroup(self::CREATIVE_URL, $advertiser_id, $group_id, $params);
    }

    /**
     * 设置项目预算信息
     *
     * @param $advertiser_id
     * @param $group_id
     * @param array $params
     * @return int
     * @throws ErrorException
     */
    public function setBudget($advertiser_id, $group_id, array $params)
    {
        return $this->setGroup(self::BUDGET_URL, $advertiser_id, $group_id, $params);
    }

    /**
     * @param $advertiser_id
     * @param $group_id
     * @param array $params
     * @param $url
     * @return int
     * @throws ErrorException
     */
    protected function setGroup($url, $advertiser_id, $group_id, array $params)
    {
        $params += compact('advertiser_id', 'group_id');

        $response = Http::httpPostJson($url, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['group_id'];
    }

    /**
     * 更新项目状态
     *
     * @param int $advertiser_id
     * @param array $group_ids
     * @param string $opt_status
     * @return array
     * @throws ErrorException
     */
    public function updateStatus($advertiser_id, array $group_ids, $opt_status)
    {

        $params = compact('group_ids', 'advertiser_id', 'opt_status');

        $response = Http::httpPostJson(self::GROUP_STATUS_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['group_ids'];
    }

    /**
     * 更新计划状态
     * https://ad.oceanengine.com/openapi/doc/index.html?id=1699
     *
     * @param $advertiser_id
     * @param $ad_id
     * @param $params
     * @return int
     * @throws ErrorException
     */
    public function updateAd($advertiser_id, $ad_id, $params)
    {
        $params += compact('advertiser_id', 'ad_id');

        $response = Http::httpPostJson(self::AD_STATUS_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['ad_id'];
    }

    /**
     * 获取项目计划列表
     *
     * @param $advertiser_id
     * @param $group_id
     * @return array
     * @throws ErrorException
     */
    public function getGroupAdList($advertiser_id, $group_id)
    {
        $params = compact('advertiser_id', 'group_id');

        $response = Http::httpGetJson(self::GROUP_AD_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['ads'];
    }

    /**
     * 获取项目素材信息
     *
     * @param $advertiser_id
     * @param $group_id
     * @return array
     * @throws ErrorException
     */
    public function getGroupMaterial($advertiser_id, $group_id)
    {
        $params = compact('advertiser_id', 'group_id');

        $response = Http::httpGetJson(self::GROUP_MATERIAL_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }

    /**
     * 更新计划目标成本
     *
     * @param $advertiser_id
     * @param $ad_id
     * @param $bid
     * @return int
     * @throws ErrorException
     */
    public function setAdBid($advertiser_id, $ad_id, $bid)
    {
        $params = compact('advertiser_id', 'ad_id', 'bid');

        $response = Http::httpPostJson(self::AD_BID_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['ad_id'];
    }

    /**
     * 追加项目计划预算
     *
     * @param $advertiser_id
     * @param $group_id
     * @param array $ad_ids
     * @param float $budget 追加的预算
     * @return array
     * @throws ErrorException
     */
    public function setAdsBudget($advertiser_id, $group_id, array $ad_ids, $budget)
    {
        $params = compact('advertiser_id', 'group_id', 'ad_ids', 'budget');

        $response = Http::httpPostJson(self::ADS_BUDGET_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['ad_ids'];
    }

    /**
     * 取消追加计划预算
     *
     * @param $advertiser_id
     * @param $ad_id
     * @return int
     * @throws ErrorException
     */
    public function cancelAdBudget($advertiser_id, $ad_id)
    {
        $params = compact('advertiser_id', 'ad_id');

        $response = Http::httpPostJson(self::CANCEL_AD_BUDGET_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['ad_id'];
    }

    /**
     * @param $advertiser_id
     * @return array
     * @throws ErrorException
     */
    public function getAdBudget($advertiser_id)
    {
        $response = Http::httpGetJson(self::AD_BUDGET_GET_URL, compact('advertiser_id'))
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['group2data'];
    }

}