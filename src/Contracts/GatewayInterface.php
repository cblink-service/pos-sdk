<?php

/*
 * This file is part of the cblinkservice//pos-sdk.
 *
 * (c) jinjun <757258777@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace CblinkService\PosSdk\Contracts;

interface GatewayInterface
{
    /**
     * 创建会员.
     *
     * @return mixed
     */
    public function createMember(array $params);

    /**
     * 修改会员.
     *
     * @return mixed
     */
    public function saveMember(array $params);

    /**
     * 湖区会员.
     *
     * @return mixed
     */
    public function queryMember(array $params);

    /**
     * 推送积分.
     *
     * @return mixed
     */
    public function pushPoint(array $params);

    /**
     * 修改会员账户信息.
     *
     * @return mixed
     */
    public function changeBalance(array $params);

    /**
     * 获取门店.
     *
     * @return mixed
     */
    public function queryShop(array $params);

    /**
     * 获取商品
     *
     * @return mixed
     */
    public function queryProduct(array $params);

    /**
     * 推送订单.
     *
     * @return mixed
     */
    public function pushOrder(array $params);

    /**
     * 获取订单.
     *
     * @param array $data
     *
     * @return mixed
     */
    public function queryOrder(array $params);
}
