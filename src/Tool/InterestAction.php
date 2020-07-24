<?php

namespace Sywzj\TTOvertrue\Tool;


use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

class InterestAction
{

    /**
     * @var AccessToken
     */
    protected $access_token;

    const ACTION_CATEGORY_URL = '/2/tools/interest_action/action/category/';
    const ACTION_KEYWORD_URL = '/2/tools/interest_action/action/keyword/';
    const INTEREST_CATEGORY_URL = '/2/tools/interest_action/interest/category/';
    const INTEREST_KEYWORD_URL = '/2/tools/interest_action/interest/keyword/';
    const ID_2_WORD_URL = '/2/tools/interest_action/id2word/';
    const KEYWORD_SUGGEST_URL = '/2/tools/interest_action/keyword/suggest/';

    /**
     * @param  $access_token
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 行为类目
     *
     * @param $advertiser_id
     * @param $action_scene
     * @param $action_days
     * @return array
     * @throws ErrorException
     */
    public function actionCategories($advertiser_id, $action_scene, $action_days)
    {
        $param = [
            'advertiser_id' => $advertiser_id,
            'action_scene' => $action_scene,
            'action_days' => $action_days,
        ];

        $response = Http::httpGetJson(static::ACTION_CATEGORY_URL, $param)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }


    /**
     * 行为关键词查询
     *
     * @param $query_words
     * @param $advertiser_id
     * @param $action_scene
     * @param $action_days
     * @return mixed
     * @throws ErrorException
     */
    public function actionKeyword($advertiser_id, $query_words, $action_scene, $action_days)
    {
        $param = [
            'advertiser_id' => $advertiser_id,
            'action_scene' => $action_scene,
            'action_days' => $action_days,
            'query_words' => $query_words,
        ];

        $response = Http::httpGetJson(static::ACTION_KEYWORD_URL, $param)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['list'];
    }

    /**
     * 兴趣类目查询
     *
     * @param $advertiser_id
     * @return mixed
     * @throws ErrorException
     */
    public function interestCategory($advertiser_id)
    {
        $response = Http::httpGetJson(static::INTEREST_CATEGORY_URL, ['advertiser_id' => $advertiser_id])
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }

    /**
     * 兴趣关键词查询
     *
     * @param $advertiser_id
     * @param $query_words
     * @return mixed
     * @throws ErrorException
     */
    public function interestKeyword($advertiser_id, $query_words)
    {
        $params = [
            'advertiser_id' => $advertiser_id,
            'query_words' => $query_words
        ];
        $response = Http::httpGetJson(static::INTEREST_KEYWORD_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data']['list'];
    }

    /**
     * 兴趣行为类目关键词id转词
     *
     * @param $params
     * @return mixed
     * @throws ErrorException
     */
    public function id2Word($params)
    {
        $response = Http::httpGetJson(static::ID_2_WORD_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }

    /**
     * 获取行为兴趣推荐关键词
     *
     * @param $params
     * @return mixed
     * @throws ErrorException
     */
    public function keywordSuggest($params)
    {
        $response = Http::httpGetJson(static::KEYWORD_SUGGEST_URL, $params)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response['data'];
    }
}