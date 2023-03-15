<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\HttpServer\Contract\RequestInterface;

class BaseController extends AbstractController
{

    /**
     * @var ResponseInterface
     */
    #[Inject()]
    protected $_response;

    /**
     * @var RequestInterface
     */
    #[Inject()]
    protected $_request;
    
    public function outputJson($data = [], $count = null, $code = 0, $msg = 'success')
    {
        $output = ['code' => $code, 'msg' => $msg, 'data' => $data];
        if (!is_null($count)) {
            $output['count'] = $count;
        }
        return $this->_response->json($output);
    }
    
    public function isAjax()
    {
        if ($this->_request->header('X-Requested-With') == 'XMLHttpRequest') {
            return true;
        }
        return false;
    }
    
}
