<?php

namespace Sywzj\TTOvertrue\Ads;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class Creative
 * @package Sywzj\TTOvertrue\Ads
 * 广告创意相关
 */
class Creative extends ArrayCollection
{
    protected $access_token;

    const GET_URL = '/2/creative/read_v2/';
    const CREATE_URL = '/2/creative/create_v2/';
    const UPDATE_URL = '/2/creative/update_v2/';
    const STATUS_URL = '/2/creative/update/status/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 创建广告创意
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function createCreative(array $item = [])
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
     * 修改广告创意信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function updateCreative($item)
    {
        $response = Http::httpPostJson(static::UPDATE_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }

    /**
     * 获取广告创意信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function getCreative(array $item)
    {
        $item['page'] = empty($item['page']) ? 1 : $item['page'];
        $item['page_size'] = empty($item['page_size']) ? 100 : $item['page_size'];

        if (!empty($item['filtering'])) {
            $item['filtering'] = json_encode($item['filtering']);
        }

        $item['fields'] =
            empty($item['fields'])
                ? ["creative_id", "ad_id", "advertiser_id", "status", "opt_status", "image_mode", "title", "creative_word_ids", "third_party_id", "image_ids", "image_id", "video_id", "audit_reject_reason", "materials"]
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
     * 获取所有的创意信息
     * @return array
     * @throws \Exception
     */
    public function getAllCreative($params)
    {
        $data = $this->getCreative($params)->get('data');
        $res = $data['list'];
        if($data['page_info']['total_page'] == 1) return $res;
        for($i = 2; $i <= $data['page_info']['total_page'];$i++) {
            try{
                $params['page'] = $i;
                $data = $this->getCreative($params)->get('data');
                $res = array_merge($res, $data['list']);
            }catch (\Exception $e) {
                return array_merge($res, []);
            }
        }
        return $res;
    }


    /**
     * 更新广告计划信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function statusCreative(array $item)
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