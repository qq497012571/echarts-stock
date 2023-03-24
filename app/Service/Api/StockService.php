<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Service\Api;

use App\Exception\ServiceException;
use App\Library\StockApiSupport\XueqiuApi;
use App\Library\Utils\ArrayHelper;
use App\Model\StockMark;
use App\Model\User;
use App\Model\UserStock;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Contract\SessionInterface;
use Hyperf\Utils\Codec\Json;

class StockService
{
    /**
     * @var HandlerStackFactory
     */
    #[Inject()]
    public $stackFactory;

    /**
     * @var SessionInterface
     */
    #[Inject()]
    public $session;

    /**
     * 获取k线
     * @param $code
     * @param $ma
     * @param $limit
     */
    public function get($code, $ma, $limit, $timestamp)
    {
        $user = $this->session->get('user');
        $xueqiuApi = new XueqiuApi($user['token'], $user['xueqiu_cookie']);
        $klines = $xueqiuApi->getKline($code, $ma, $limit, $timestamp);
        return $klines;
    }

    /**
     * 获取自选列表
     */
    public function list($page, $pagesize, $syncStock = 0)
    {
        $user = $this->session->get('user');
        $xueqiuApi = new XueqiuApi($user['token'], $user['xueqiu_cookie']);

        if ($syncStock) {
            $result = $xueqiuApi->getList();
            if (isset($result['data']['stocks'])) {
                foreach ($result['data']['stocks'] as $stock) {
                    $userStock = UserStock::query()->where('user_id', $user['id'])->where('code', $stock['symbol'])->first();
                    if (!$userStock) {
                        $userStock = new UserStock();
                        $userStock->user_id = $user['id'];
                        $userStock->code = $stock['symbol'];
                        $userStock->name = $stock['name'];
                        $userStock->created_at = $stock['created'] / 1000;
                        $userStock->save();
                    }
                }
            }
        }

        $userQuery = UserStock::query()->where('user_id', $user['id']);
        $count = $userQuery->count();
        $list = $userQuery->offset(($page - 1) * $pagesize)->limit($pagesize)->orderBy('created_at', 'desc')->get();

        if (count($list)) {
            $result = $xueqiuApi->quote(implode(',', ArrayHelper::array_column($list, 'code')));

            $resultMap = [];
            foreach ($result['data']['items'] as $res) {
                if (isset($res['quote']['symbol'])) {
                    $resultMap[$res['quote']['symbol']] = $res['quote'];
                }
            }

            foreach ($list as &$v) {
                $v->current = $resultMap[$v['code']]['current'] ?? '';
                $v->percent = $resultMap[$v['code']]['percent'] ?? '';
                $v->chg = $resultMap[$v['code']]['chg'] ?? '';
            }

        }


        return [$list, $count];
    }

    /**
     * 添加股票到自选
     * @param $code
     */
    public function add($code)
    {

    }

    /**
     * 删除自选股票
     * @param $code
     */
    public function cancel($code)
    {
        $user = $this->session->get('user');
        $userStock = UserStock::query()->where('code', $code)->where('user_id', $user['id'])->first();
        $userStock->delete();
        if ($user['xueqiu_cookie']) {
            $xueqiuApi = new XueqiuApi($user['token'], $user['xueqiu_cookie']);
            $result = $xueqiuApi->cancel($code);
            var_dump($result);
        }
    }

    /**
     * 搜索股票
     * @param $word
     */
    public function serach($word)
    {
    }


    /**
     * 新增标记
     */
    public function addMark($code, $overlayId, $option, $markType)
    {
        $user = $this->session->get('user');
        $stockMark = StockMark::query()->where('code', $code)->where('user_id', $user['id'])->where('overlay_id', $overlayId)->first();
        if (is_null($stockMark)) {
            $stockMark = new StockMark();
            $stockMark->code = $code;
            $stockMark->overlay_id = $overlayId;
            $stockMark->mark_type = $markType;
        }
        $stockMark->option = $option;
        $stockMark->user_id = $user['id'];
        $stockMark->save();
    }

    /**
     * 删除标记
     */
    public function removeMark($code, $overlayId)
    {
        $user = $this->session->get('user');
        StockMark::query()->where('code', $code)->where('user_id', $user['id'])->where('overlay_id', $overlayId)->delete();
    }

    /**
     * 获取marks
     */
    public function marks($code)
    {
        $user = $this->session->get('user');
        return StockMark::query()->where('user_id', $user['id'])->where('code', $code)->get()->toArray();
    }


    /**
     * 添加预警
     */
    public function addAlarm()
    {

    }
    
}
