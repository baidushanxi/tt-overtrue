<?php

namespace Sywzj\TTOvertrue\Tool;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class Conversion
 * @package Sywzj\TTOverture\Tool
 * 工具---转化目标管理
 */
class Conversion extends ArrayCollection
{
    protected $access_token;

    const AD_CONVERT_CREATE = "/2/tools/ad_convert/create/";//创建转化ID
    const AD_CONVERT_UPDATE = "/2/tools/ad_convert/update/";//修改转化ID
    const AD_CONVERT_UPDATE_STATUS = "/2/tools/ad_convert/update_status/";//更新转化状态
    const AD_CONVERT_SELECT = "/2/tools/ad_convert/select/";//查询计划可用转化ID
    const ADV_CONVERT_SELECT = "/2/tools/adv_convert/select/";//转化ID列表
    const AD_CONVERT_READ = "/2/tools/ad_convert/read/";//查询转化详细信息
    const AD_CONVERT_PUSH = "/2/tools/ad_convert/push/";//转化ID推送

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * 创建转化ID
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertCreate(array $item = [])
    {
        $response = Http::httpPostJson(static::AD_CONVERT_CREATE, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 修改转化ID
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertUpdate(array $item = [])
    {
        $response = Http::httpPostJson(static::AD_CONVERT_UPDATE, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 更新转化状态
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertUpdateStatus(array $item = [])
    {
        $response = Http::httpPostJson(static::AD_CONVERT_UPDATE_STATUS, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 查询计划可用转化ID
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertSelect(array $item = [])
    {
        $response = Http::httpGetJson(static::AD_CONVERT_SELECT, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 转化ID列表
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function advConvertSelect(array $item = [])
    {
        $response = Http::httpGetJson(static::ADV_CONVERT_SELECT, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 查询转化详细信息
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertRead(array $item = [])
    {
        $response = Http::httpGetJson(static::AD_CONVERT_READ, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 转化ID推送
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function adConvertPush(array $item = [])
    {
        $response = Http::httpPostJson(static::AD_CONVERT_PUSH, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

}