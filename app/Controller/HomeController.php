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
use App\Service\Api\UserProfileService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\View\RenderInterface;
use App\Middleware\HttpAuthMiddleware;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Contract\SessionInterface;


#[AutoController()]
class HomeController extends BaseController
{
    /**
     * @var UserService
     */
    #[Inject()]
    public $userService;

    /**
     * @var UserProfileService
     */
    #[Inject()]
    public $userProfileService;

    /**
     * @var SessionInterface
     */
    #[Inject()]
    public $session;


    #[Middlewares([HttpAuthMiddleware::class])]
    public function index(RenderInterface $render)
    {
        return $render->render('layouts/app', ['user' => $this->session->get('user')]);
    }

    #[Middlewares([HttpAuthMiddleware::class])]
    public function profile(RenderInterface $render)
    {
        return $render->render('home/profile', ['user' => $this->userProfileService->profile()]);
    }


    #[Middlewares([HttpAuthMiddleware::class])]
    public function qrcode(RenderInterface $render)
    {
        return $render->render('home/qrcode');
    }

    /**
     * 登录
     */
    public function login(RequestInterface $request, ResponseInterface $response, RenderInterface $render)
    {
        if ($request->getMethod() == 'POST') {
            $this->userService->login($request->post('email'));
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
