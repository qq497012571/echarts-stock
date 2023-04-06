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

use App\Model\StockMark;
use App\Model\UserStock;
use App\Model\StockAlarm;
use App\Model\StockMarket;
use Hyperf\Di\Annotation\Inject;
use App\Library\Utils\ArrayHelper;
use Hyperf\Contract\SessionInterface;
use Hyperf\Guzzle\HandlerStackFactory;
use App\Library\StockApiSupport\XueqiuApi;

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
    public function list($page, $pagesize, $field, $orderBy)
    {
        $user = $this->session->get('user');

        $userQuery = UserStock::query()->leftJoin('stock_market', 'stock_market.symbol', '=', 'user_stock.code')->where('user_stock.user_id', $user['id']);
        $fields = ['user_stock.id', 'user_stock.code', 'stock_market.name', 'stock_market.current', 'stock_market.percent', 'stock_market.chg'];
        $count = $userQuery->count();
        $list = $userQuery->offset(($page - 1) * $pagesize)->limit($pagesize)->orderBy($field, $orderBy)->get($fields);
        return [$list, $count];
    }

    /**
     * 添加股票到自选
     * @param $code
     */
    public function add($code)
    {
        $user = $this->session->get('user');
        $userStock = UserStock::query()->where('code', $code)->where('user_id', $user['id'])->first();

        if (is_null($userStock)) {
            $loginXueqiu = $user['xueqiu_cookie'] ? true : false;
            if ($loginXueqiu) {
                $xueqiuApi = new XueqiuApi($user['email'], $user['xueqiu_cookie']);
            } else {
                $xueqiuApi = new XueqiuApi($user['email']);
            }
            $quoteDetail = $xueqiuApi->quote($code);
            foreach ($quoteDetail['data']['items'] as $item) {
                $market = $item['market'];
                $quote = $item['quote'];

                $userStock = new UserStock();
                $userStock->user_id = $user['id'];
                $userStock->code = $code;
                $userStock->name = $quote['name'];
                $userStock->save();

                $market = StockMarket::query()->where('symbol', $quote['symbol'])->first();
                if (!$market) {
                    $market = new StockMarket();
                }
                $market->code = $quote['code'];
                $market->symbol = $quote['symbol'];
                $market->name = $quote['name'];
                $market->status = $quote['status'];
                $market->exchange = $quote['exchange'];
                $market->current = $quote['current'];
                $market->open = $quote['open'];
                $market->high = $quote['high'];
                $market->low = $quote['low'];
                $market->percent = $quote['percent'];
                $market->chg = $quote['chg'];
                $market->volume = $quote['volume'];
                $market->amount = $quote['amount'];
                $market->market_capital = $quote['market_capital'] ?? 0;
                $market->timestamp = $quote['timestamp'];
                $market->save();

                break;
            }
        }
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
    public function search($code, $page, $size)
    {
        $user = $this->session->get('user');
        $xueqiuApi = new XueqiuApi($user['email']);
        $result = $xueqiuApi->search($code, $page, $size);
        $data = [];
        if (count($result['stocks'])) {
            $userStockList = UserStock::query()->where('user_id', $user['id'])->get();
            $userIndexBy = ArrayHelper::array_index($userStockList, 'code');
            var_dump("================================================");
            var_dump($userIndexBy);
            var_dump("================================================");
        }
        foreach ($result['stocks'] as $stock) {
            $stock['hasexist'] = isset($userIndexBy[$stock['code']]) ? 1 : 0;
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
        return StockMark::query()->where('user_id', $user['id'])->where('code', $code)->where('pause', 0)->get()->toArray();
    }
}
