<?php
/**
 * Created by PhpStorm.
 * User: wangzhongjie  Email: baidushanxi@vip.qq.com
 * Date: 2019/4/30
 * Time: 上午11:31
 */

namespace Sywzj\TTOvertrue\Ads;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\Bridge\ErrorException;

class Ad extends ArrayCollection
{
    protected $access_token;

    const GET_URL = '/2/ad/get/';
    const CREATE_URL = '/2/ad/create/';
    const UPDATE_URL = '/2/ad/update/';
    const STATUS_URL = '/2/ad/update/status/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 创建广告计划
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function create(array $item = [])
    {
        $response = Http::httpPostJson(static::CREATE_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }


    /**
     * 修改广告计划信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function update($item)
    {
        if (empty($item['modify_time'])) {
            $data = $this->get([
                'advertiser_id' => $item['advertiser_id'],
                'filtering' => ['ids' => [$item['ad_id']]],
                'fields' => ["modify_time"],
            ]);

            if (empty($data['data']) || empty($data['data']['list'])) {
                throw new ErrorException('get ad modify time error');
            }
            $item['modify_time'] = $data['data']['list']['0']['modify_time'];
        }

        $response = Http::httpPostJson(static::UPDATE_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }


    /**
     * 获取广告计划信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function get(array $item)
    {
        $item['page'] = empty($item['page']) ? 1 : $item['page'];
        $item['page_size'] = empty($item['page_size']) ? 100 : $item['page_size'];

        $item['filtering'] = !empty($item['filtering']) ? json_encode($item['filtering']) : [];
        $item['fields'] =
            empty($item['fields'])
                ? ["id", "name", "budget", "budget_mode", "status", "opt_status","open_url", "modify_time", "start_time", "end_time", "bid","advertiser_id", "pricing", "flow_control_mode", "download_url","inventory_type", "schedule_type", "app_type", "cpa_bid","cpa_skip_first_phrase", "audience", "external_url", "package","campaign_id", "ad_modify_time", "ad_create_time","audit_reject_reason", "retargeting_type", "retargeting_tags","convert_id", "interest_tags", "hide_if_converted"]
                : $item['fields'];

        $response = Http::httpGetJson(static::GET_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }

    /**
     * 更新广告计划信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function status(array $item)
    {
        $response = Http::httpPostJson(static::STATUS_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }




}