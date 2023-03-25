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
namespace App\Service;

use App\Model\User;
use App\Constant\RedisKey;
use Hyperf\Redis\RedisFactory;
use Hyperf\Di\Annotation\Inject;
use App\Exception\ServiceException;
use Hyperf\Contract\SessionInterface;


class UserService
{
    /**
     * @var RedisFactory
     */
    #[Inject()]
    public $redisFactory;

    /**
     * @var SessionInterface
     */
    #[Inject()]
    public $session;


    /**
     * 用户登录
     * @param $email 邮箱
     */
    public function login($email)
    {   
        $user = User::query()->where('email', $email)->first();
        if (!$user) {
            $user = new User();
            $user->email = $email;
            $user->name = $email;
            $user->save();
        }
        $this->session->set('user', $user->toArray());
    }

    /**
     * 用户退出
     */
    public function logout()
    {   
        $this->session->clear();
    }

}
