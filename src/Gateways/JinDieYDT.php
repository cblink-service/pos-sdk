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

class JinDieYDT
{
    use HasHttpRequest;

    protected $config;

    protected $baseUri = 'http://vip100.kingdee.com/CY-PosOnlineOpenApi/canDaoTakeoutApi/operate.action';

    public function __construct($config)
    {
        $this->config = $config;
    }

    /**
     * 推送订单.
     *
     * @return array|mixed
     */
    public function pushOrder(array $data)
    {
        $data['actionName'] = 'candao.order.addOrder';

        return $this->sendRequest($data);
    }

    /**
     * 修改订单状态
     *
     * @return array
     */
    public function saveOrderStatus(array $data)
    {
        $data['actionName'] = 'candao.order.updateOrderStatus';

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
        $data['actionName'] = 'candao.order.updateRefundStatus';

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
