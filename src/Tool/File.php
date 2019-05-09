<?php

namespace Sywzj\TTOverture\Tool;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

/**
 * Class File
 * @package Sywzj\TTOverture\Tool
 * 工具---文件管理
 */
class File extends ArrayCollection
{
    protected $access_token;

    const ADVERTISER_URL = "/2/file/image/advertiser/";//广告主图片
    const IMAGE_AD = "/2/file/image/ad/";//上传广告图片
    const VIDEO_AD = "/2/file/video/ad/";//上传视频
    const IMAGE_AD_GET = "/2/file/image/ad/get/";//查询图片信息
    const VIDEO_AD_GET = "/2/file/video/ad/get/";//查询视频信息
    const VIDEO_COVER = "/2/tools/video_cover/suggest/";//获取视频智能封面
    const IMAGE_GET = "/2/file/image/get/";//获取图片蔬菜库
    const VIDEO_GET = "/2/file/video/get/";//获取视频素材库

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 广告主图片
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function imageAdvertiser(array $item = [])
    {
        $response = Http::httpPostJson(static::ADVERTISER_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 上传广告图片
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function imageAd(array $item = [])
    {
        $response = Http::httpPostJson(static::IMAGE_AD, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 上传视频
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function videoAd(array $item = [])
    {
        $response = Http::httpPostJson(static::VIDEO_AD, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 查询图片信息
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function imageAdGet(array $item = [])
    {
        $response = Http::httpGetJson(static::IMAGE_AD_GET, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 查询视频信息
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function videoAdGet(array $item = [])
    {
        $response = Http::httpGetJson(static::VIDEO_AD_GET, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 获取视频智能封面
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function videoCoverSuggest(array $item = [])
    {
        $response = Http::httpGetJson(static::VIDEO_COVER, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 获取图片蔬菜库
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function imageGet(array $item = [])
    {
        $response = Http::httpGetJson(static::IMAGE_GET, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 获取视频素材库
     * @param array $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function videoGet(array $item = [])
    {
        $response = Http::httpGetJson(static::VIDEO_GET, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

}