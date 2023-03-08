<?php
declare(strict_types=1);

namespace App\Controller;

use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\View\RenderInterface;

#[AutoController()]
class ViewController
{
    public function index(RenderInterface $render)
    {
        // 从请求中获得 id 参数
        // $id = $request->input('id', 1);
        return $render->render('index', ['name' => 1]);
    }
}