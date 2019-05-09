<?php
namespace Sywzj\TTOvertrue\Ads;

use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class Campaign
 * @package Sywzj\TTOvertrue\Ads
 * 广告组相关
 */
class Campaign
{
    protected $access_token;

    const GET_URL = '/2/campaign/get/';
    const CREATE_URL = '/2/campaign/create/';
    const UPDATE_URL = '/2/campaign/update/';
    const STATUS_URL = '/2/campaign/update/status/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 创建广告组
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
     * 修改广告组信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function update($item)
    {
        if (empty($item['modify_time'])) {
            $data = $this->get([
                'advertiser_id' => $item['advertiser_id'],
                'filtering' => ['ids' => [$item['campaign_id']]],
                'fields' => ["modify_time"],
            ]);

            if (empty($data['data']) || empty($data['data']['list'])) {
                throw new ErrorException('get campaign modify time error');
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
     * 获取广告组信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function get(array $item)
    {
        $item['page'] = empty($item['page']) ? 1 : $item['page'];
        $item['page_size'] = empty($item['page_size']) ? 100 : $item['page_size'];

        if (!empty($item['filtering'])){
            $item['filtering'] = json_encode($item['filtering']);
        }

        $item['fields'] = empty($item['fields']) ? ["id", "name", "budget", "budget_mode", "landing_type", "status", "modify_time"] : $item['fields'];

        $response = Http::httpGetJson(static::GET_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }

    /**
     * 更新广告组信息
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