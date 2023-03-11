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

    public function get(RequestInterface $request)
    {
        $data = $this->stockService->get($request->query('code', ''), $request->query('klt', '101'), $request->query('limit', '9999'));
        return $this->outputJson($data);
    }


    public function list(RequestInterface $request)
    {
        $data = $this->stockService->list();
        return $this->outputJson($data);
    }

}
