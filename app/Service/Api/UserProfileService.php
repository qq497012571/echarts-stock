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

namespace App\Service\Api;

use App\Library\StockApiSupport\XueqiuApi;
use App\Model\User;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Contract\SessionInterface;

class UserProfileService
{

    /**
     * @var SessionInterface
     */
    #[Inject()]
    public $session;

    public function profile()
    {
        return $this->session->get('user');
    }

    /**
     * 雪球app登录二维码
     */
    public function qrcode()
    {
        $user = $this->session->get('user');
        $xueqiu = new XueqiuApi($user['email']);
        $result = $xueqiu->generateQrCode();
        if ($result['result_code'] != 200 || !isset($result['data']['qr_code'])) {
            throw new \Exception('生成二维码失败: ' . $result['message']);
        }
        return $result['data']['qr_code'];
    }

    
    /**
     * 雪球登录二维码 扫码登录状态查询
     */
    public function qrcodeState($code)
    {
        $user = $this->session->get('user');
        
        $xueqiu = new XueqiuApi($user['email']);
        $result = $xueqiu->queryQrCodeState($code);
        if ($result['result_code'] != 200 || !isset($result['data']['status'])) {
            throw new \Exception('查询二维码状态接口失败: ' . json_encode($result));
        }

        if ($result['data']['status'] == 2) {
            $user = User::query()->where('id', $user['id'])->first();
            $user->xueqiu_cookie = $xueqiu->cookieToString();
            $user->xueqiu_result = json_encode($result['data'], JSON_UNESCAPED_UNICODE);
            $user->save();
            $this->session->set('user', $user->toArray());
        }

        return $result;
    }

}
