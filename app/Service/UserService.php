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

class UserService
{
    /**
     * @var RedisFactory
     */
    #[Inject()]
    public $redisFactory;

    /**
     * 用户登录
     * @param $token
     */
    public function login($token)
    {   
        $user = User::query()->where('token', $token)->first();
        if (!$user) {
            throw new ServiceException("用户不存在", 500);
        }
        $redis = make(RedisFactory::class)->get('default');
        $redis->set(RedisKey::ACCESS_TOKEN_KEY . $token, json_encode($user->toArray()));

        return $token;
    }

}
