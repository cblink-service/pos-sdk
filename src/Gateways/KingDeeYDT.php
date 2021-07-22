<?php

/*
 * This file is part of the cblinkservice//pos-sdk.
 *
 * (c) jinjun <757258777@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace CblinkService\PosSdk\Gateways;

use CblinkService\PosSdk\Traits\HasHttpRequest;
use Ramsey\Uuid\Uuid;

class KingDeeYDT
{
    use HasHttpRequest;

    protected $config;

    protected $baseUri;

    public function __construct($config)
    {
        $this->config = $config;
        $this->baseUri = $config['debug'] ?
            'http://vip100.kingdee.com/CY-PosOnlineOpenApi/canDaoTakeoutApi/operate.action':
            'https://shishen.kingdee.com/CY-PosOnlineOpenApi/thirdPosBillApi/operate.action';
    }

    /**
     * 推送订单.
     *
     * @return array|mixed
     */
    public function pushOrder(array $data)
    {
        $data['actionName'] = $this->config['debug'] ? 'candao.order.addOrder' : 'addOrder';

        return $this->sendRequest($data);
    }

    /**
     * 修改订单状态
     *
     * @return array
     */
    public function saveOrderStatus(array $data)
    {
        $data['actionName'] =  $this->config['debug'] ? 'candao.order.updateOrderStatus' : 'updateOrderStatus';

        return $this->sendRequest($data);
    }

    /**
     * 申请退款.
     *
     * @param array $data
     *
     * @return array
     */
    public function updateRefundStatus($data)
    {
        $data['actionName'] = $this->config['debug'] ? 'candao.order.updateRefundStatus' : 'updateRefundStatus';

        return $this->sendRequest($data);
    }

    /**
     * 发送请求
     *
     * @param $data
     * @param $method
     *
     * @return array
     */
    public function sendRequest($data = [], $method = 'post')
    {
        // 处理签名
        $data['timestamp'] = time() * 1000;
        $data['ticket'] = Uuid::uuid4()->toString();
        $data['accessKey'] = $this->config['accessKey'];
        $data['sign'] = $this->setSign($data);

        return $this->requestApi($data, $method);
    }

    public function setSign($data)
    {
        $string = sprintf('%s%s%s%s%s',
            $data['accessKey'],
            $data['actionName'],
            $this->config['secret'],
            $data['timestamp'],
            json_encode($data['data'], JSON_UNESCAPED_UNICODE)
        );

        return md5($string);
    }

    /**
     * 发送请求
     *
     * @param $data
     *  @param $method
     *
     * @return array
     */
    public function requestApi($data, $method)
    {
        // 发送请求
        return $this->request($method, $this->getBaseUri(), [
            'json' => $data,
        ]);
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }
}
