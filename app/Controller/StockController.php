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

namespace App\Controller;

use App\Service\StockService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;
use App\Middleware\HttpAuthMiddleware;
use App\ViewHelper\StockViewHelper;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
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
     * 自选股表格
     */
    public function list(RequestInterface $request, ResponseInterface $response, RenderInterface $render)
    {
        return $render->render('stock/list');
    }

    /**
     * k线展示
     */
    public function kline(RequestInterface $request, RenderInterface $render, ResponseInterface $response)
    {
        return $render->render('stock/kline');
    }
}
