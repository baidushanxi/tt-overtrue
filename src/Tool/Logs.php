<?php


namespace Sywzj\TTOvertrue\Tool;


use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\Http;

class Logs extends ArrayCollection
{
    protected $access_token;

    const TOOLS_LOG_SEARCH = "/2/tools/log_search/";//获取评论

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }

    public function getLogs($start, $end, $advertiser_id, $object_id = [])
    {
        $totalPage = 1;
        $page = 1;
        do {
            try {
                $response = Http::httpGetJson(static::TOOLS_LOG_SEARCH,
                    [
                        'advertiser_id' => $advertiser_id,
                        'start' => $start,
                        'end' => $end,
                        'page' => $this->get('page') ?: $page,
                        'page_size' => 100
                    ]
                )
                    ->withAccessToken($this->access_token)
                    ->send();

                if (0 != $response['code']) {
                    $totalPage = 0;
                    continue;
                }
                $totalPage = array_get($response, 'data.page_info.total_page');
                yield array_get($response, 'data.logs');
            } catch (\Exception $e) {
                yield $e;
            }
            $this->set('page', $page + 1);
        } while ($this->get('page') <= $totalPage);
    }

}