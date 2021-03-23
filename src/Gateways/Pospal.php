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

class Pospal implements GatewayInterface
{
    use HasHttpRequest;

    protected $config;

    protected $baseUri = 'https://pospal';

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function createMember(array $params)
    {
    }

    public function saveMember(array $params)
    {
    }

    public function queryMember(array $params)
    {
    }

    public function changeBalance(array $params)
    {
    }

    public function pushPoint(array $params)
    {
    }

    public function queryShop(array $params)
    {
    }

    public function queryProduct(array $params)
    {
    }

    public function pushOrder(array $params)
    {
    }

    public function queryOrder(array $params)
    {
    }

    /**
     * 银豹发送请求
     *
     * @param $uri
     * @param $data
     *
     * @return array
     */
    public function sendRequest($uri, $data)
    {
        $data = array_merge($data, $this->config);

        $data = array_filter($data);

        $data['sign'] = $this->generateSign($data);

        unset($data['secret']);

        return $this->post(sprintf('%s/%s', $this->getBaseUri(), $uri), $data);
    }

    /**
     * 银豹 签名.
     */
    private function generateSign(array $params)
    {
        // 按 kay 值排序
        return '';
    }

    public function getBaseUri()
    {
        return $this->baseUri;
    }
}
