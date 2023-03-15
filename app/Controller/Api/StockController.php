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

        $data = $this->stockService->get($code, $ma, $limit);
        return $this->outputJson($data);
    }

    /**
     * 获取自选列表
     */
    public function list(RequestInterface $request)
    {
        list($data, $count)  = $this->stockService->list($request->query('page', 1), $request->query('limit', 20));
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
            $code = $request->input('code');
            $value = $request->input('value');
            $markType = $request->input('mark_type');
            $markOption = $request->input('mark_option');
            $this->stockService->addMark($code, $value, $markType, $markOption);
            return $this->outputJson([]);
        }
    }

}
