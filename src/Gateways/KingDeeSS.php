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
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use Symfony\Component\Cache\CacheItem;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class KingDeeSS
{
    use HasHttpRequest;

    protected $config;

    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $expireSecond = 7000;

    protected $baseUri;

    /**
     * @var Client
     */
    private $client;
    /**
     * @var CookieJar
     */
    private $cookieJar;

    public function __construct($config)
    {
        $this->baseUri = $config['debug'] ? 'http://kdcy2.kingdee.com/k3cloud/' : $config['xk_base_uri'];
        $this->config = $config;
    }

    protected function getCookies()
    {
        /** @var CacheItem $cacheCookie */
        $cacheCookie = $this->getCache()->getItem($this->getCacheKey());

        // 缓存是否存在
        if ($cacheCookie->get()) {
            $cookieArray = json_decode($cacheCookie->get(), true);
            $this->cookieJar = CookieJar::fromArray($cookieArray, parse_url($this->baseUri)['host']);
        }

        if (!$this->cookieJar) {
            $this->cookieJar = new CookieJar();
        }

        return $this->cookieJar;
    }

    /**
     * @return array
     */
    protected function getBaseOptions()
    {
        $options = [
            'base_uri' => method_exists($this, 'getBaseUri') ? $this->getBaseUri() : '',
            'timeout' => method_exists($this, 'getTimeout') ? $this->getTimeout() : 10.0,
            'cookies' => $this->getCookies(),
        ];

        return $options;
    }

    /**
     * 获取门店.
     *
     * @return array|mixed
     */
    public function queryShop(array $data)
    {
        $url = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.ExecuteBillQuery.common.kdsvc';

        return $this->sendRequest($url, $data);
    }

    /**
     * 同步食品
     *
     * @return array|mixed
     */
    public function queryProduct(array $data)
    {
        $url = 'Kingdee.BOS.WebApi.ServicesStub.DynamicFormService.ExecuteBillQuery.common.kdsvc';

        return $this->sendRequest($url, $data);
    }

    /**
     * 获取账套.
     *
     * @return array
     */
    public function queryEatsun()
    {
        return $this->get('getalldatacenters.eatsun');
    }

    /**
     * 登陆.
     *
     * @param $account
     *
     * @return mixed
     */
    public function login($account)
    {
        return $this->getCache()->get($this->getCacheKey(), function (ItemInterface $item) use ($account) {
            // 请求 账套
            $this->request('post', sprintf('%s%s', $this->getBaseUri(), 'Kingdee.BOS.WebApi.ServicesStub.AuthService.ValidateUser.common.kdsvc'),
                [
                    'json' => $account,
                ]
            );

            $item->expiresAfter($this->expireSecond);

            $cookieArr = $this->cookieJar->toArray();

            foreach ($cookieArr as $cookieItem) {
                $cookies[$cookieItem['Name']] = $cookieItem['Value'];
            }

            return json_encode($cookies);
        });
    }

    /**
     * 发送请求
     *
     * @param $uri
     * @param $data
     * @param $method
     *
     * @return array
     */
    public function sendRequest($uri, $data = [], $method = 'post')
    {
        $response = $this->requestApi($uri, $data, $method);

        $result = json_decode($response, true);

        return $result;
    }

    /**
     * 发送请求
     *
     * @param $uri
     * @param $data
     *  @param $method
     *
     * @return array
     */
    public function requestApi($uri, $data, $method)
    {
        // token 获取
        $this->getAccessToken();

        return $this->request($method, sprintf('%s%s', $this->getBaseUri(), $uri), [
            'json' => $data,
        ]);
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
        // 请求 账套
        $account = $this->getAccountIdServer();

        // 请求 token
        return $this->login($account);
    }

    /**
     * 设置缓存实例.
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }

    /**
     * 获取缓存.
     *
     * @return CacheInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * 获取缓存 key.
     *
     * @return string
     */
    protected function getCacheKey()
    {
        return sprintf('%s_cookie', $this->config['username']);
    }

    /**
     * 请求接口获取所有账套.
     */
    public function getAccountIdServer()
    {
        return [
            'acctID' => $this->config['acct_id'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'lcid' => $this->config['lcid'],
        ];
    }
}
