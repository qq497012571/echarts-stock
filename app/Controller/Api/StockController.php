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

    /**
     * 获取自选列表
     */
    public function list(RequestInterface $request)
    {
        list($data, $count)  = $this->stockService->list($request->query('page', 1), $request->query('limit', 20), $request->query('sync_stock', 20));
        return $this->outputJson($data, $count);
    }

    public function marks(RequestInterface $request)
    {
        $data = $this->stockService->marks($request->query('code'));
        return $this->outputJson($data);
    }


    public function addMark(RequestInterface $request)
    {
        if ($request->getMethod() == 'POST') {
            $this->stockService->addMark($request->input('code'), $request->input('overlay_id'), $request->input('option'), $request->input('mark_type'));
            return $this->outputJson([]);
        }
    }

    public function removeMark(RequestInterface $request)
    {
        $this->stockService->removeMark($request->input('code'), $request->input('overlay_id'));
        return $this->outputJson([]);
    }

}
