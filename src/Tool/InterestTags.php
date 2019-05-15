<?php

namespace Sywzj\TTOvertrue\Tool;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sywzj\TTOvertrue\AccessToken\AccessToken;

/**
 * Class InterestTags
 * @package Sywzj\TTOvertrue\InterestTags
 * 兴趣关键词相关
 */
class InterestTags extends ArrayCollection
{
    protected $access_token;

    const GET_URL = '/2/tools/interest_tags/select/';
    const WORD_TO_ID_URL = '/2/tools/interest_tags/word2id/';

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 兴趣关键词转id
     * @param array $words
     * @param string $advertiser_id
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function word2id(array $words, $advertiser_id)
    {
        if (empty($words)) {
            return [];
        }

        $param = [
            "json" => [
                'advertiser_id' => $advertiser_id,
                'words' => $words
            ]
        ];

        $response = Http::httpGetJson(static::WORD_TO_ID_URL, $param)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
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
            'advertiser_id'=>$advertiser_id
        ];

        $response = Http::httpGetJson(static::GET_URL, $param)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }


}