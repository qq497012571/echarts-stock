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

use App\Library\StockApiSupport\XueqiuApi;
use App\Library\Utils\ArrayHelper;
use App\Model\StockAlarm;
use App\Model\StockMark;
use App\Model\UserStock;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Guzzle\HandlerStackFactory;
use Hyperf\Contract\SessionInterface;

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
        $xueqiuApi = new XueqiuApi($user['email'], $user['xueqiu_cookie']);
        $klines = $xueqiuApi->getKline($code, $ma, $limit, $timestamp);
        return $klines;
    }

    /**
     * 获取自选列表
     */
    public function list($page, $pagesize, $syncStock = 0)
    {
        $user = $this->session->get('user');
        if ($syncStock) {
            $xueqiuApi = new XueqiuApi($user['email'], $user['xueqiu_cookie']);
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

        $userQuery = UserStock::query()->leftJoin('stock_market', 'stock_market.symbol', '=', 'user_stock.code')->where('user_stock.user_id', $user['id']);
        $fields = ['user_stock.id', 'user_stock.code', 'stock_market.name', 'stock_market.current', 'stock_market.percent', 'stock_market.chg'];
        $count = $userQuery->count();
        $list = $userQuery->offset(($page - 1) * $pagesize)->limit($pagesize)->orderBy('user_stock.created_at', 'desc')->get($fields);
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
            $xueqiuApi = new XueqiuApi($user['email'], $user['xueqiu_cookie']);
            $result = $xueqiuApi->cancel($code);
        }
    }

    /**
     * 搜索股票
     * @param $code
     */
    public function search($code)
    {
        $user = $this->session->get('user');
        $xueqiuApi = new XueqiuApi($user['email'], $user['xueqiu_cookie']);
        $result = $xueqiuApi->search($code);
        $data = [];
        foreach ($result['stocks'] as $stock) {
            $data[] = $stock;
        }
        return $data;
    }

    /**
     * 新增标记
     */
    public function addMark($code, $overlayId, $option, $markType, $alarmForm)
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


        if ($markType == 'alarm_line') {

            $alarmForm = json_decode($alarmForm, true);

            if ($stockMark->alarm_id) {
                $stockAlarm = StockAlarm::query()->where('id', $stockMark->alarm_id)->first();
            } else {
                $stockAlarm = new StockAlarm();
            }

            $stockAlarm->user_id = $user['id'];
            $stockAlarm->code = $code;
            $stockAlarm->price = $alarmForm['price'];
            $stockAlarm->timing_type = $alarmForm['timing_type'];
            $stockAlarm->time_type = 1;
            $stockAlarm->status = 0;
            $stockAlarm->expire_time = time() + 25200; // 默认七天后过期
            $stockAlarm->push_channel = 'web'; // 默认七天后过期
            $stockAlarm->remark = $alarmForm['remark'];
            $stockAlarm->save();

            $stockMark->alarm_id = $stockAlarm->id;
        }
        $stockMark->save();
    }

    public function saveAlarm($data)
    {
        $user = $this->session->get('user');
        $stockMark = StockMark::query()->where('user_id', $user['id'])->where('overlay_id', $data['overlay_id'])->first();
        $alarm = StockAlarm::query()->where('id', $stockMark->alarm_id)->first();
        if ($alarm) {
            $alarm->timing_type = $data['timing_type'];
            $alarm->price = $data['price'];
            $alarm->time_type = $data['time_type'];
            $alarm->title = $data['title'];
            $alarm->remark = $data['remark'];
            $alarm->expire_time = strtotime($data['expire_time']);
            $alarm->save();
        }
    }

    /**
     * 删除标记
     */
    public function removeMark($code, $overlayId)
    {
        $user = $this->session->get('user');
        $stockMark = StockMark::query()->where('code', $code)->where('user_id', $user['id'])->where('overlay_id', $overlayId)->first();
        if ($stockMark->alarm_id) {
            $alarm = StockAlarm::query()->where('id', $stockMark->alarm_id)->first();
            $alarm->is_del = 1;
            $alarm->save();
        }
        $stockMark->delete();
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
