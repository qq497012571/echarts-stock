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

#[AutoController()]
class HomeController extends BaseController
{
    /**
     * @var UserService
     */
    #[Inject()]
    public $userService;


    #[Middlewares([HttpAuthMiddleware::class])]
    public function index(RenderInterface $render)
    {
        echo 123123123,"\n";
        return $render->render('layouts/app');
    }

    /**
     * 登录
     */
    public function login(RequestInterface $request, ResponseInterface $response, RenderInterface $render)
    {
        if ($request->getMethod() == 'POST') {
            $this->userService->login($request->post('token'));
            return $response->redirect('/');
        }
        return $render->render('home/login');
    }


    /**
     * 退出登录
     */
    public function logout(ResponseInterface $response)
    {
        $this->userService->logout();
        return $response->redirect('/login');
    }

}
