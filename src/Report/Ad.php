<?php

namespace Sywzj\TTOvertrue\Report;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\Http;
use Sywzj\TTOvertrue\Bridge\ErrorException;

/**
 * Class Ad
 * @package Sywzj\TTOvertrue\Report
 * 广告计划报表
 */
class Ad extends ArrayCollection
{
    protected $access_token;

    protected $group_field = [];
    protected $time_granularity = '';

    const REPORT_URL = '/2/report/ad/get/';//广告计划报表

    protected $required = ['advertiser_id'];

    protected $group_by = ['STAT_GROUP_BY_FIELD_ID', 'STAT_GROUP_BY_FIELD_STAT_TIME'];

    protected $time_granularitys = ["STAT_TIME_GRANULARITY_DAILY", "STAT_TIME_GRANULARITY_HOURLY"];

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    /**
     * 获取广告计划报表信息
     * @param $item
     * @return \Doctrine\Common\Collections\ArrayCollection|string
     * @throws \Exception
     */
    public function report()
    {
        $item = $this->resolveOptions();

        $response = Http::httpGetJson(static::REPORT_URL, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }
        return $response;
    }


    /**
     * 获取所有的报表信息
     * @return array
     * @throws \Exception
     */
    public function allReport()
    {
        $report = $this->report()->get('data');
        $res = $report['list'];
        if($report['page_info']['total_page'] == 1) return $res;
        for($i = 2; $i <= $report['page_info']['total_page'];$i++) {
            try{
                $this->set('page',$i);
                $report = $this->report()->get('data');
                $res = array_merge($res, $report['list']);
            }catch (\Exception $e) {
                return array_merge($res, []);
            }
        }
        return $res;
    }



    /**
     * 获取向头条请求的参数
     * @return array
     */
    public function getRequestData()
    {
        return $this->resolveOptions();
    }

    /**
     * 设置按哪个字段group by
     * @param string $group_field
     * @return $this
     */
    public function groupBy(array $group_fields)
    {
        $this->group_fields = $group_fields;
        return $this;
    }

    /**
     * 设置时间粒度
     * @param string $group_field
     * @return $this
     */
    public function timeGranularity(string $time_granularity)
    {
        $this->time_granularity = $time_granularity;
        return $this;
    }

    /**
     * 合并和校验参数.
     */
    public function resolveOptions()
    {
        $defaults = [
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'page' => $this->get('page') ?: 1,
            'page_size' => $this->get('page_size') ?: 100,
            'time_granularity' => $this->time_granularity ?: current($this->time_granularitys),
            'group_by' => $this->group_fields ?: [current($this->group_by)],
            'filtering' => [],
        ];

        $resolver = new OptionsResolver();
        $resolver
            ->setRequired($this->required)
            ->setDefaults($defaults)
            ->setAllowedTypes('group_by', [
                'array'
            ])
            ->setAllowedValues('time_granularity', $this->time_granularitys);

        $options = $resolver->resolve($this->toArray());

        if (!empty($this->get('filtering'))) {
            $options['filtering'] = json_encode($options['filtering']);
        } else {
            unset($options['filtering']);
        }

        return $options;
    }

}