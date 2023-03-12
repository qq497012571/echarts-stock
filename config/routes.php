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
use Hyperf\HttpServer\Router\Router;
use App\Middleware\HttpAuthMiddleware;

Router::addRoute(['GET'], '/', 'App\Controller\HomeController@index', ['middleware' => [HttpAuthMiddleware::class]]);
Router::addRoute(['GET', 'POST'], '/login', 'App\Controller\HomeController@login');
Router::addRoute(['GET', 'POST'], '/logout', 'App\Controller\HomeController@logout', ['middleware' => [HttpAuthMiddleware::class]]);

Router::get('/favicon.ico', function () {
    return '';
});
