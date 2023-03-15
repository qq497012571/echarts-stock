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

use App\Constant\Stock;
use Hyperf\HttpServer\Annotation\AutoController;
use App\Library\StockApiSupport\XueqiuApi;

#[AutoController()]
class TestController extends BaseController
{
    public function index()
    {
        $api = new XueqiuApi('device_id=b3ae28a4538fcd61c7e20638ac15b835; s=bj1456hoqm; xq_a_token=56bd49d70e5d46d8a393d0717c0112224c56ef9d; xqat=56bd49d70e5d46d8a393d0717c0112224c56ef9d; xq_r_token=ff815f68dd127ee8c075c0d30a50f074bff4334e; xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgwNzY3ODUxLCJjdG0iOjE2Nzg3OTQxODQ0NTIsImNpZCI6ImQ5ZDBuNEFadXAifQ.MxogQfrMv16YUxCBmNHILuwEcWD5zS1mLuTfNbwsLbJkjVHf3QM-a4eqr9oNXrzibeiBEZWniYsZZB9volsCyq_Bto6EydGx1BE2QtEpBQNEz9rx221gqeDOyxePRHvunSiCEtmim5bnXDj1g0sPjS9ZMv64ZeomG-I2pNIFZzn6m56eRzA4ljM4faPSCKkQ3LTzA2fJBC6ahPqNT80Rv0GgvZxQSJ2z4ujrxjBarxxVIrN3_L3p1f3z8iEGM_wR9oh3DK1fzc0P3aXjXA6phHMfGt3Qg0Ct4F9MwpuQTZqvtQgO_daDL-JUlCJT4EcMw4UnaF6whG4D3OwTyc-xxQ; xq_is_login=1; u=2806260066; Hm_lvt_1db88642e346389874251b5a1eded6e3=1678791988,1678860662; snbim_minify=true; Hm_lpvt_1db88642e346389874251b5a1eded6e3=1678865442');
        return $api->getKline('SH000001', Stock::QUERY_DAY, Stock::QUERY_DAY_LIMIT);
    }

    
    public function list()
    {
        $api = new XueqiuApi('device_id=b3ae28a4538fcd61c7e20638ac15b835; s=bj1456hoqm; xq_a_token=56bd49d70e5d46d8a393d0717c0112224c56ef9d; xqat=56bd49d70e5d46d8a393d0717c0112224c56ef9d; xq_r_token=ff815f68dd127ee8c075c0d30a50f074bff4334e; xq_id_token=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJ1aWQiOjI4MDYyNjAwNjYsImlzcyI6InVjIiwiZXhwIjoxNjgwNzY3ODUxLCJjdG0iOjE2Nzg3OTQxODQ0NTIsImNpZCI6ImQ5ZDBuNEFadXAifQ.MxogQfrMv16YUxCBmNHILuwEcWD5zS1mLuTfNbwsLbJkjVHf3QM-a4eqr9oNXrzibeiBEZWniYsZZB9volsCyq_Bto6EydGx1BE2QtEpBQNEz9rx221gqeDOyxePRHvunSiCEtmim5bnXDj1g0sPjS9ZMv64ZeomG-I2pNIFZzn6m56eRzA4ljM4faPSCKkQ3LTzA2fJBC6ahPqNT80Rv0GgvZxQSJ2z4ujrxjBarxxVIrN3_L3p1f3z8iEGM_wR9oh3DK1fzc0P3aXjXA6phHMfGt3Qg0Ct4F9MwpuQTZqvtQgO_daDL-JUlCJT4EcMw4UnaF6whG4D3OwTyc-xxQ; xq_is_login=1; u=2806260066; Hm_lvt_1db88642e346389874251b5a1eded6e3=1678791988,1678860662; snbim_minify=true; Hm_lpvt_1db88642e346389874251b5a1eded6e3=1678865442');
        return $api->getList();
    }
    
}
