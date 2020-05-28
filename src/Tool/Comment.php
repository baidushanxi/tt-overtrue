<?php


namespace Sywzj\TTOvertrue\Tool;

use Doctrine\Common\Collections\ArrayCollection;
use Sywzj\TTOvertrue\AccessToken\AccessToken;
use Sywzj\TTOvertrue\Bridge\ErrorException;
use Sywzj\TTOvertrue\Bridge\Http;

class Comment extends ArrayCollection
{
    protected $access_token;

    const AD_COMMENT_GET = "/2/tools/comment/get/";//获取评论
    const AD_COMMENT_REPLY_GET = "/2/tools/comment_reply/get/";//获取评论
    const AD_COMMENT_OPERATE = "/2/tools/comment/operate/";//评论操作
    const AD_COMMENT_TERMS_BANNED_GET = "/2/tools/comment/terms_banned/get/";   //获取屏蔽词
    const AD_COMMENT_TERMS_BANNED_UPDATE = "/2/tools/comment/terms_banned/update/";//更新屏蔽词
    const AD_COMMENT_TERMS_BANNED_DELETE = "/2/tools/comment/terms_banned/delete/";//删除屏蔽词
    const AD_COMMENT_TERMS_BANNED_ADD = "/2/tools/comment/terms_banned/add/";   //添加屏蔽词

    /**
     * 构造方法.
     */
    public function __construct(AccessToken $access_token)
    {
        $this->access_token = $access_token;
    }


    /**
     * 获取评论回复列表
     *
     * @param $item
     * @return \Generator
     */
    public function getcommentReply($item)
    {
        $totalPage = 1;
        $page = 1;
        do {
            try {
                $response = Http::httpGetJson(static::AD_COMMENT_REPLY_GET,
                    array_merge(
                        $item,
                        [
                            'page' => $page,
                            'page_size' => empty($item['page_size']) ? 100 : $item['page_size']
                        ]
                    ))
                    ->withAccessToken($this->access_token)
                    ->send();

                if (0 != $response['code']) {
                    $totalPage = 0;
                    continue;
                }
                $totalPage = array_get($response, 'data.page_info.total_page');
                yield array_get($response, 'data.reply_list');
            } catch (\Exception $e) {
                yield $e;
            }
            $page = $page + 1;
        } while ($page <= $totalPage);
    }


    /**
     * 获取评论列表
     *
     * @param $item
     * @return \Generator
     */
    public function getComment($item)
    {
        $totalPage = 1;
        $page = 1;
        do {
            try {
                $response = Http::httpGetJson(static::AD_COMMENT_GET,
                    array_merge(
                        $item,
                        [
                            'page' => $page,
                            'page_size' => empty($item['page_size']) ? 100 : $item['page_size']
                        ]
                    ))
                    ->withAccessToken($this->access_token)
                    ->send();

                if (0 != $response['code']) {
                    $totalPage = 0;
                    continue;
                }
                $totalPage = array_get($response, 'data.page_info.total_page');
                yield array_get($response, 'data.comments_list');
            } catch (\Exception $e) {
                yield $e;
            }
            $page = $page + 1;
        } while ($page <= $totalPage);
    }


    public function operate($item)
    {
        $response = Http::httpPostJson(static::AD_COMMENT_OPERATE, $item)
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return $response;
    }

    /**
     * 回复评论，可多选
     * @param $advertiser_id
     * @param $comment_ids
     * @param $replay_text
     */
    public function replay($advertiser_id, $comment_ids, $replay_text)
    {
        return $this->operate([
            'inventory_type' => 'INVENTORY_AWEME_FEED',
            'operate_type' => 'REPLY',
            'comment_ids' => array_wrap($comment_ids),
            'reply_text' => $replay_text,
        ]);
    }

    /**
     * 回复评论下评论
     * @param $advertiser_id
     * @param $comment_ids 最多一个
     * @param $reply_id
     * @param $replay_text
     */
    public function replayToReplay($advertiser_id, $comment_ids, $reply_id, $replay_text)
    {
        $comment_ids = array_wrap($comment_ids);
        return $this->operate([
            'inventory_type' => 'INVENTORY_AWEME_FEED',
            'operate_type' => 'REPLY_TO_REPLY',
            'comment_ids' => array_slice(array_wrap($comment_ids)),
            'reply_text' => $replay_text,
            'reply_id' => $reply_id,
        ]);
    }


    /**
     * 是否置顶某评论
     * @param $advertiser_id
     * @param $comment_ids 只允许传入一个
     * @param bool $isStick
     */
    public function stick($advertiser_id, $comment_ids, $isStick = true)
    {
        $comment_ids = array_wrap($comment_ids);
        return $this->operate([
            'inventory_type' => 'INVENTORY_AWEME_FEED',
            'operate_type' => $isStick == true ? 'STICK_ON_TOP' : 'CANCEL_STICK',
            'comment_ids' => array_slice($comment_ids, 0),
            'advertiser_id' => $advertiser_id,
        ]);
    }


    /**
     * 隐藏用户评论
     * @param $advertiser_id
     * @param $comment_ids 最多50个
     */
    public function hide($advertiser_id, $comment_ids)
    {
        return $this->operate([
            'inventory_type' => 'INVENTORY_AWEME_FEED',
            'operate_type' => 'HIDE',
            'comment_ids' => array_wrap($comment_ids),
            'advertiser_id' => $advertiser_id,
        ]);
    }


    /**
     * 拉黑用户
     * @param $advertiser_id
     * @param $comment_ids
     */
    public function blockUser($advertiser_id, $comment_ids)
    {
        $comment_ids = array_wrap($comment_ids);
        return $this->operate([
            'inventory_type' => 'INVENTORY_AWEME_FEED',
            'operate_type' => 'BLOCK_USERS',
            'comment_ids' => array_slice($comment_ids, 0),
            'advertiser_id' => $advertiser_id,
        ]);
    }


    /**
     * 添加屏蔽词
     * @param $advertiser_id
     * @param $terms
     * @return bool
     */
    public function addTermsBanned($advertiser_id, $terms)
    {
        $response = Http::httpPostJson(
            static::AD_COMMENT_TERMS_BANNED_ADD,
            ['advertiser_id' => $advertiser_id, 'terms' => $terms]
        )
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return true;
    }


    /**
     * 获取屏蔽词列表
     * @param $advertiser_id
     * @return ArrayCollection|\Generator|string
     */
    public function getTermsBanned($advertiser_id)
    {
        $totalPage = 1;
        $page = 1;
        do {
            try {
                $response = Http::httpGetJson(
                    static::AD_COMMENT_TERMS_BANNED_GET,
                    [
                        'advertiser_id' => $advertiser_id,
                        'page' => $this->get('page') ?: $page,
                        'page_size' => $this->get('page_size') ?: 100
                    ])
                    ->withAccessToken($this->access_token)
                    ->send();

                if (0 != $response['code']) {
                    $totalPage = 0;
                    continue;
                }

                $totalPage = array_get($response, 'data.page_info.total_page');

                yield array_get($response, 'data.terms');
            } catch (\Exception $e) {
                \Log::info($e->getMessage() . '|' . $e->getLine());
                // 返回异常
                yield $e;
            }
            $this->set('page', $page + 1);
        } while ($this->get('page') <= $totalPage);
    }

    /**
     * 更新屏蔽词
     * @param $advertiser_id
     * @param $origin_terms
     * @param $new_terms
     * @return bool
     */
    public function updateTermsBanned($advertiser_id, $origin_terms, $new_terms)
    {
        $response = Http::httpPostJson(
            static::AD_COMMENT_TERMS_BANNED_DELETE,
            ['advertiser_id' => $advertiser_id, 'origin_terms' => $origin_terms, 'new_terms' => $new_terms]
        )
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return true;
    }


    /**
     * 删除屏蔽词
     * @param $advertiser_id
     * @param $terms
     * @return bool
     */
    public function deleteTermsBanned($advertiser_id, $terms)
    {
        $response = Http::httpPostJson(
            static::AD_COMMENT_TERMS_BANNED_DELETE,
            ['advertiser_id' => $advertiser_id, 'terms' => $terms,]
        )
            ->withAccessToken($this->access_token)
            ->send();

        if (0 != $response['code']) {
            throw new ErrorException($response['message'], $response['code']);
        }

        return true;
    }

}