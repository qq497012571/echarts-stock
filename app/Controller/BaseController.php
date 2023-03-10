<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;

class BaseController extends AbstractController
{

    /**
     * @var ResponseInterface
     */
    #[Inject()]
    protected $_response;

    public function outputJson($data = [], $code = 200, $msg = 'success')
    {
        return $this->_response->json(['code' => $code, 'msg' => $msg, 'data' => $data]);
    }
}
