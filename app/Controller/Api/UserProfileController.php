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

use App\Service\Api\UserProfileService;
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
class UserProfileController extends BaseController
{
    /**
     * @var UserProfileService
     */
    #[Inject()]
    public $userProfileService;

    /**
     * 获取二维码
     */
    public function qrcode(RequestInterface $request)
    {
        return $this->outputJson(['qrcode' => $this->userProfileService->qrcode()]);
    }

    /**
     * 获取二维码状态
     */
    public function qrcodeState(RequestInterface $request)
    {
        return $this->outputJson(['result' => $this->userProfileService->qrcodeState($request->input('code'))]);
    }

}
