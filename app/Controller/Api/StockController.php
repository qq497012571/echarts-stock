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

namespace App\Controller\Api;

use App\Constant\Stock;
use App\Service\Api\StockService;
use Hyperf\Di\Annotation\Inject;
use App\Controller\BaseController;
use App\Middleware\HttpAuthMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

#[Middlewares([HttpAuthMiddleware::class])]
#[AutoController()]
class StockController extends BaseController
{
    /**
     * @var StockService
     */
    #[Inject()]
    public $stockService;

    /**
     * 获取k线
     * @param RequestInterface $request
     */
    public function klines(RequestInterface $request)
    {
        $code = $request->input('code');
        $ma = $request->input('ma', Stock::QUERY_DAY);
        $limit = $request->input('limit', Stock::QUERY_DAY_LIMIT);
        $timestamp = $request->input('timestamp');

        $data = $this->stockService->get($code, $ma, $limit, $timestamp);
        return $this->outputJson($data);
    }

    public function marks(RequestInterface $request)
    {
        $data = $this->stockService->marks($request->query('code'));
        return $this->outputJson($data);
    }

    public function addMark(RequestInterface $request)
    {
        if ($request->getMethod() == 'POST') {
            $this->stockService->addMark($request->input('code'), $request->input('overlay_id'), $request->input('option'), $request->input('mark_type'), $request->input('alarm_form', '{}'));
            return $this->outputJson([]);
        }
    }

    public function saveAlarm(RequestInterface $request)
    {
        if ($request->getMethod() == 'POST') {
            $this->stockService->saveAlarm($request->inputs(['timing_type', 'price', 'time_type', 'expire_time', 'title', 'remark', 'overlay_id']));
            return $this->outputJson([]);
        }
    }


    /**
     * 删除overlay
     * @param RequestInterface $request
     */
    public function removeMark(RequestInterface $request)
    {
        $this->stockService->removeMark($request->input('code'), $request->input('overlay_id'));
        return $this->outputJson([]);
    }


    /**
     * 搜索股票
     * @param RequestInterface $request
     */
    public function search(RequestInterface $request)
    {
        return $this->outputJson($this->stockService->search($request->input('code'),  $request->input('page'), $request->input('limit')));
    }

}
