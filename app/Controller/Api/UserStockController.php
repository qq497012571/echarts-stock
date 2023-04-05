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

use App\Service\Api\StockService;
use Hyperf\Di\Annotation\Inject;
use App\Controller\BaseController;
use App\Middleware\HttpAuthMiddleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;


/**
 * 用户自选股
 */
#[Middlewares([HttpAuthMiddleware::class])]
#[AutoController()]
class UserStockController extends BaseController
{
    /**
     * @var StockService
     */
    #[Inject()]
    public $stockService;

    /**
     * 获取自选列表
     */
    public function list(RequestInterface $request)
    {
        list($data, $count)  = $this->stockService->list($request->query('page', 1), $request->query('limit', 20), $request->query('field', 'user_stock.created_at'), $request->query('order', 'desc'));
        return $this->outputJson($data, $count);
    }

    /**
     * 添加自选股票
     * @param RequestInterface $request
     */
    public function add(RequestInterface $request)
    {
        $this->stockService->add($request->input('code'));
        return $this->outputJson([]);
    }

    /**
     * 删除自选股票
     * @param RequestInterface $request
     */
    public function cancel(RequestInterface $request)
    {
        $this->stockService->cancel($request->input('code'));
        return $this->outputJson([]);
    }

}
