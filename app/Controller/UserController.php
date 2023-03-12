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

use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;
use App\Middleware\HttpAuthMiddleware;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;


/**
 * 我的持仓
 */
#[Middlewares([HttpAuthMiddleware::class])]
#[AutoController()]
class UserController extends BaseController
{

    /**
     * @var UserService
     */
    #[Inject()]
    public $userService;
    
   
    public function index(RequestInterface $request, ResponseInterface $response, RenderInterface $render)
    {
        return $render->render('user/index');
    }


    

}
