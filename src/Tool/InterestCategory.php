<?php

namespace Sywzj\TTOvertrue\Tool;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class InterestCategory
 * @package Sywzj\TTOvertrue\InterestTags
 * 兴趣类目查询
 */
class InterestCategory extends ArrayCollection
{
    protected $access_token;

    const GET_URL = '/2/tools/industry/get/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 获取词包信息
     * @param string $advertiser_id
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function get($advertiser_id)
    {
        $param = [

            'type' => 'ADVERTISER'
        ];

        $response = Http::httpGetJson(static::GET_URL, $param)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['list'];
    }


}