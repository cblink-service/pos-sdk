<?php

/*
 * This file is part of the cblinkservice//pos-sdk.
 *
 * (c) jinjun <757258777@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace CblinkService\PosSdk\Gateways;

use CblinkService\PosSdk\Contracts\GatewayInterface;
use CblinkService\PosSdk\Traits\HasHttpRequest;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class JinDieYDT implements GatewayInterface
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
     * @param array $data
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
     * @param array $data
     * @return array
     */
    public function saveOrderStatus(array $data)
    {
        $data['actionName'] = 'candao.order.updateOrderStatus';
        return $this->sendRequest($data);
    }

    /**
     * 申请退款
     *
     * @param array $data
     * @return array
     */
    public function updateRefundStatus($data)
    {
        $data['actionName'] = 'candao.order.updateRefundStatus';
        return $this->sendRequest($data);
    }

    /**
     * 推送积分.
     *
     * @return array|mixed
     */
    public function pushPoint(array $data)
    {
        return ;
    }

    /**
     * 获取订单.
     *
     * @return array|mixed
     */
    public function queryOrder(array $data)
    {
        return ;
    }

    /**
     * 获取门店.
     *
     * @return array|mixed
     */
    public function queryShop(array $data)
    {
        return ;
    }

    /**
     * 同步食品
     *
     * @return array|mixed
     */
    public function queryProduct(array $data)
    {
        return ;
    }

    /**
     * 获取账套.
     *
     * @return array | mixed
     */
    public function queryEatsun()
    {
        return ;
    }

    /**
     * 获取会员卡
     *
     * @return array|mixed
     */
    public function queryMember(array $params)
    {
        return ;
    }

    /**
     * 修改客户信息.
     *
     * @return array|mixed
     */
    public function saveMember(array $params)
    {
        return ;
    }

    /**
     * 调整卡金额积分.
     */
    public function changeBalance(array $params)
    {
    }

    /**
     * 添加会员及会员卡
     */
    public function createMember(array $params)
    {
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
        $data['ticket'] =  Uuid::uuid4()->getHex()->toString();
        $data['accessKey'] = $this->config['accessKey'];
        $data['sign'] = $this->setSign($data);

        return $this->requestApi($data, $method);
    }

    public function setSign($data)
    {
        $string = sprintf( '%s%s%s%s%s',
            $data['accessKey'],
            $data['actionName'],
            $this->config['secret'],
            $data['timestamp'],
            json_encode($data['data'],JSON_UNESCAPED_UNICODE)
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
        // token 获取
        return $this->request($method, $this->getBaseUri(), [
            'json' => $data,
        ]);
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }

}
