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
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class KingDeeXK
{
    use HasHttpRequest;

    protected $config;

    /**
     * @var CacheInterface
     */
    protected $cache;

    protected $expireSecond = 7000;

    protected $baseUri;

    public function __construct($config)
    {
        $this->baseUri = $config['debug'] ? 'http://kdcy2.kingdee.com/k3cloud/' : $config['xk_base_uri'];

        $this->config = $config;
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
        $response = $this->postJson(sprintf('%s%s', $this->getBaseUri(), 'login.eatsun'), [
            'UserToken' => '',
            'ActionName' => 'Login',
            'PostData' => [$account],
        ]);

        return json_decode($response, true);
    }

    /**
     * 获取卡类型.
     *
     * @return array
     */
    public function queryGetCardType(array $params)
    {
        $data['ActionName'] = 'Common/getcardtype';
        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 获取等级.
     *
     * @return array
     */
    public function queryGuestLevel(array $params)
    {
        $data['ActionName'] = 'Common/getguestlevel';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 获取会员卡
     *
     * @return array
     */
    public function queryMember(array $params)
    {
        $data['ActionName'] = 'Membership/get';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 修改客户信息.
     *
     * @return array|mixed
     */
    public function saveMember(array $params)
    {
        $data['ActionName'] = 'Guest/update';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 会员卡操作流水.
     *
     * @return array
     */
    public function queryMemberCardRecord(array $params)
    {
        $data['ActionName'] = 'Membership/getcardjournal';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 调整卡金额积分.
     *
     * @return array
     */
    public function changeBalance(array $params)
    {
        $data['ActionName'] = 'Membership/changebalance';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 添加会员及会员卡
     *
     * @return array
     */
    public function createMember(array $params)
    {
        $data['ActionName'] = 'Membership/add';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
    }

    /**
     * 会员余额消费.
     *
     * @return array
     */
    public function memberBalanceConsume(array $params)
    {
        $data['ActionName'] = 'membership/Consume';

        $data['PostData'] = $params;

        return $this->sendRequest('v1.eatsun', $data);
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
    public function sendRequest($uri, $data = [])
    {
        $response = $this->requestApi($uri, $data);

        $result = json_decode($response, true);

        // 返回 token 失效就再次重新获取 token 发送
        if (false == $result['Result'] && array_key_exists('ErrorCode', $result['ReturnValue']) && 101 == $result['ReturnValue']['ErrorCode']) {
            // 删除缓存
            $this->getCache()->delete($this->getCacheKey());

            $response = $this->requestApi($uri, $data);

            $result = json_decode($response, true);
        }

        return $result;
    }

    /**
     * 发送请求
     *
     * @param $uri
     * @param $data
     *
     * @return array
     */
    public function requestApi($uri, $data)
    {
        // token 获取
        $data['UserToken'] = $this->getAccessToken();

        return $this->postJson(sprintf('%s%s', $this->getBaseUri(), $uri), $data);
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
            // 请求 账套
            $account = $this->getAccountIdServer();

            // 请求 token
            $tokenResult = $this->login($account);

            $item->expiresAfter($this->expireSecond);

            return $tokenResult['ReturnValue'] ?? null;
        });
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
        return sprintf('%s_access_token', $this->config['user_name']);
    }

    /**
     * 请求接口获取所有账套.
     */
    public function getAccountIdServer()
    {
        return [
            'AccountID' => $this->config['acct_id'],
            'UserName' => $this->config['user_name'],
            'Password' => $this->config['password'],
        ];
    }
}
