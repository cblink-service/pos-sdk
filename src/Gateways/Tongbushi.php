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
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class Tongbushi implements GatewayInterface
{
    use HasHttpRequest;

    protected $config;

    protected $cache;

    protected $baseUri = 'https://tongbushi';

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
     * 同步时发送请求
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
     * 同步时 签名.
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

    /**
     * 获取 token.
     *
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->getCache()->get($this->getCacheKey(), function (ItemInterface $item) {
            // 请求 token
            $tokenResult = $this->getAccessTokenFromServer();

            $item->expiresAfter($this->getExpireSecond($tokenResult));

            return $tokenResult['data']['access_token'] ?? null;
        });
    }

    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    public function getCache()
    {
        return $this->cache;
    }

    protected function getCacheKey()
    {
        return sprintf('%s:access_token', $this->config['app_id']);
    }

    protected function getExpireSecond($tokenResult)
    {
        // 1. 获取创建时间
        $createTime = intval(intval($tokenResult['data']['expire_time'] / 1000) - 86400);

        // 2. 创建时间 加上 12 小时 减去 当前时间 == 最准确的缓存时间
        return $createTime + 43200 - time();
    }

    /**
     * 请求接口 签名.
     */
    public function getAccessTokenFromServer()
    {
        $salt = mt_rand(1000, 9999);

        $sign = $this->generateTokenSign($salt);

        $response = $this->postJson(sprintf('%s/%s', $this->getBaseUri(), 'get_access_token'),
            [
                'app_id' => $this->config['app_id'],
                'salt' => $salt,
                'signature' => $sign,
            ]
        );

        return $response;
    }

    public function generateTokenSign($salt)
    {
        // 2. 拼接字符传
        $seed = sprintf(
            'app_id=%s&salt=%s&secret_key=%s',
            $this->config['app_id'],
            $salt,
            $this->config['secret_key']
        );

        return md5(urlencode($seed));
    }
}
