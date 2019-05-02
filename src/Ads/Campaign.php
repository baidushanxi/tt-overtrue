<?php
/**
 * Created by PhpStorm.
 * User: wangzhongjie  Email: baidushanxi@vip.qq.com
 * Date: 2019/4/30
 * Time: 上午11:31
 */

namespace Sywzj\TTOvertrue\Ads;

use Sywzj\TTOvertrue\Bridge\Http;
use Sywzj\TTOvertrue\Token\AccessToken;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Campaign
{
    protected $accessToken;

    const GET_URL = 'https://ad.toutiao.com/open_api/2/campaign/get/';
    const CREATE_URL = 'https://ad.toutiao.com/open_api/2/campaign/create/';
    const UPDATE_URL = 'https://ad.toutiao.com/open_api/2/campaign/update/';
    const STATUS_URL = 'https://ad.toutiao.com/open_api/2/campaign/update/status/';

    protected $created_required = [
        'advertiser_id', 'campaign_name', 'budget_mode', 'budget', 'landing_type', 'unique_fk'
    ];


    public function __construct(AccessToken $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    public function create()
    {
        $response = Http::request('POST', static::CREATE_URL)
            ->withAccessToken($this->accessToken)
            ->withBody($this->getRequestBody())
            ->send();

    }

    /**
     * 合并和校验参数.
     */
    public function resolveOptions()
    {
        $defaults = [
            'nonce_str' => Util::getRandomString(),
            'client_ip' => Util::getClientIp(),
        ];

        $resolver = new OptionsResolver();
        $resolver
            ->setDefined($this->created_required)
            ->setAllowedValues('trade_type', $this->tradeTypes)
            ->setAllowedValues('trade_type', $this->tradeTypes)
            ->setAllowedValues('trade_type', $this->tradeTypes)
            ->setDefaults($defaults);

        return $resolver->resolve($this->toArray());
    }


    public function update()
    {

    }


    public function gets()
    {

    }


    public function delete()
    {

    }


}