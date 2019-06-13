<?php
/**
 * Created by PhpStorm.
 * User: wangzhongjie  Email: baidushanxi@vip.qq.com
 * Date: 2019/6/13
 * Time: 下午7:14
 */

namespace Sywzj\TTOvertrue\Tool;

use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;
class Dmp
{
    protected $access_token;

    const DATA_SOURCE_UPLOAD_URL = "/2/dmp/data_source/file/upload/";   //数据源文件上传
    const DATA_SOURCE_CREATE_URL = "/2/dmp/data_source/create/";        //创建数据源
    const CUSTOM_AUDIENCE_PUSH_URL = "/2/dmp/custom_audience/push_v2/";// 推送人群包
    const CUSTOM_AUDIENCE_PUBLISH_URL = "/2/dmp/custom_audience/publish/";//推送人群包



    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * 数据源上传
     * @param $item
     * @param int $item .advertiser_id 广告主ID
     * @param string $item .file file path
     * @return mixed
     */
    public function dataSourceUpload($item)
    {
        if (!file_exists($file = $item['file'] ?? '')) {
            throw new ErrorException($file . ' not exist', 500);
        }

        $info = pathinfo($file);
        $data['multipart'] = [
            [
                'name' => 'file',
                'contents' => fopen($file, 'r'),
                'filename' => $info['basename'],
            ],
            [
                'name' => 'file_signature',
                'contents' => md5_file($file),
            ],
            [
                'name' => 'advertiser_id',
                'contents' => (int)$item['advertiser_id'],
            ],
        ];

        $response = Http::httpPostJson(static::DATA_SOURCE_UPLOAD_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }


    /**
     * 创建数据源
     * @param $item
     * @param int $item .advertiser_id 广告主ID
     * @param string $item .data_source_name 数据源名称, 限30个字符内
     * @param string $item .description 数据源描述, 限256字符内
     * @param string $item .data_format 数据格式, 0: ProtocolBuffer
     * @param string $item .file_storage_type 数据存储类型, 0: API
     * @param string $item .file_paths  通过上传接口得到的文件路径
     * @return mixed
     */
    public function dataSourceCreate($item)
    {
        $response = Http::httpPostJson(static::DATA_SOURCE_CREATE_URL, array_merge([
            'data_format' => 0,
            'file_storage_type' => 0,
        ], $item))
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

}