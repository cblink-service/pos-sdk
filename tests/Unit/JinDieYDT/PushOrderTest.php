<?php

/*
 * This file is part of the cblinkservice//pos-sdk.
 *
 * (c) jinjun <757258777@qq.com>
 *
 * This source file is subject to the MIT license that is bundled.
 */

namespace Tests\Feature\Event;

use CblinkService\PosSdk\PosApi;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class PushOrderTest extends \PHPUnit\Framework\TestCase
{
    protected $appTest;

    protected function setUp(): void
    {
        parent::setUp();
        $testCofnig = require 'testConfig.php';

        $config = [
            'driver' => 'KingDeeYDT',
            'channels' => [
                'KingDeeYDT' => $testCofnig['KingDeeYDT'],
            ],
        ];

        $this->appTest = (new PosApi($config))->gateway('KingDeeYDT');
    }

    /**
     * 推送订单
     */
    public function testOrder()
    {

        // key=>9a9e68c41ebf7933
        // secret=>bb4ea81216876b0209eaf62817dc9429

        //[120512,"1006","上上签火锅店", 2000020000120512],

        //["001","米饭",10.0000000000,1,"100"],["002","面食",14.8000000000,1,"100"],["003","小炒肉",36.0000000000,1,"100"]
        $data = [
            'substoreId' => "2000020000120512",
            'data' => [
                "orderId" => "114", // 订单 id(全局唯一)
                "extOrderId" => "114",  // 平台订单 id
                "orderNo" => "113", // 平台订单号
                "thirdSn" => "2",   // 平台流水号 N
                "sn" => "#4 20210322",  // 刘会好
                "storeId" => "2000020000120512",   // 统一门店 id
                "subStoreId" => "2000020000120512",
                "storeName" => "深圳金蝶餐饮管理有限公司",  // 门店名称
                "counts" => 1,  // 购买数量
                "longitude" => "116.478140",    // 经度
                "latitude" => "40.007662",  // 纬度
                "name" => "测**",    // 收货人姓名
                "phone" => "15600493277,133",   // 手机号
                "address" => "广州市荔湾区芳村下市直街1号信义会馆5A2",   // 地址
                "takeNo" => "2",    // 取餐号
                "orderType" => 1,   // 1：外卖；2：自取；3：堂食；4：外卖预约；5：新零售；6：打包/外带
                "sendTime" => "2021-03-22 12:07:24",    // 预定时间
                "fromType" => "mtdp",        // 下单渠道
                "orderStatus" => 7, // 7：商家待接单；10：商家已接单；12：备餐中；14：配送中；16：就餐中；18：待取餐；20：取餐超时；100：订单完成；-1：订单取消；21：备餐完成
                "orderTime" => "2021-03-22 11:22:24",   // 下订单时间
                "payTime" => "2021-03-22 11:22:24", // 付款时间
                "orderDate" => "2021-03-22 11:22:24",   // 账单归属时间
                "payType" => 2, // 支付类型
                "isPayed" => true,  // 是否已支付
                "currency" => "1",  // 1 人民币 2 港版 3 美元
                "isInvoice" => false,    // 是否开发票
                // "invoice" => "金蝶软件（中国）有限公司",    // 发票抬头
                // "invoiceType" => 2,  // 发票类型
                // "taxNo" => "91440101088569460X", // 税号
                // "peopleNum" => 0,    // 就餐人数
                "isThirdDistribute" => false, // false 自配送
                "price" => "15.0",  // 订单支付总金额
                "realTimeProductPrice" => "10.0",   // 下单实时餐品总金额
                "productPrice" => "10.00",   // 餐品总额
                "deliveryFee" => "5.0", // 配送费
                "mealFee" => "0", // 餐盒费
                "discountPrice" => "0", // 优惠总金额
                "merchantPrice" => "15.0", // 商户实收金额 merchantPrice=支付金额(price)+平台补贴(thirdPlatformBearPrice)+代理商承担(agentBearPrice)-佣金(commission)-距离加价(distanceIncreaseFee)-时段加价(timeIntervalMarkUpFee)
                "merchantBearPrice" => "0",   // 商家承担优惠金额
                "thirdPlatformBearPrice" => "0", // 第三方平台承担优惠金额
                "commission" => "0", // 第三方平台佣金
                "userId" => "2056158",  // 会员 id
                "registerPhone" => "99999999999", // 用户注册电话号码
                "isStoreFirstOrder" => false,
                "isBrandFirstOrder" => false,
//                "paymentDetails"=>[
//                    "type" => 2,
//                    "money" => "15.0",
//                    "typeName" => "微信支付",
//                    "isInvoice" => false,
//                ],
                "skus" => [
                    "title"=>"甜度",
                    "name" => "三分糖",
                ],
                "products" => [
                    [
                        "pid" => "123123",   // 统一产品 id(小程序的 id)
                        "subPid" => "001",
                        "isDiscount" => false, // 是否为优惠餐品
                        "name" => "米饭",  // 餐品名称
                        "price" => "10.00",   // 1单价
                        "totalPrice" => "10.00", // 餐品总价
                        "realTimePrice" => "10.00",   // 下单实时餐品单价
                        "realTimeTotalPrice" => "10.00",  // 下单实时餐品总价
                        "num" => "1",   // 数量
                        "boxPrice" => "3.00",    // 餐盒单价
                        // "bagNo" => "1", // 袋子编号
                    ],


                ],
                 "driverStatus" => 0 // 配送状态
            ]
        ];

        $res = $this->appTest->pushOrder($data);

        $this->assertSame($res['status'], 1);
    }

    /**
     * 修改订单状态
     */
    public function testOrderStatus()
    {
        $data = [
            "storeId"=>'2000020000120512',
            'data' => [
                "orderId" => "112",
                "extOrderId" => "112",
                "orderNo" => "112",
                "storeId" => "2000020000120513",
                "subStoreId" => "2000020000120513",
                "status" => 18,
                "cancelReason" => 0
            ]];
        $res = $this->appTest->saveOrderStatus($data);

        $this->assertSame($res['status'], 1);
    }

    /**
     * 退款
     */
    public function testOrderRefund()
    {
        $data = [
            "storeId" => '123',
            'data' => [
                "refundId" => "1110",
                "orderId" => "111",
                "extOrderId" => "111",
                "orderNo" => "111",
                "storeId" => "123",
                "subStoreId" => "2000020000120512",
                "refundReason" => "商家通知我卖完了",
                "refundType" => 0,
                "refundPrice" => "15.0",
                "status" => 1,
                "consigneePhone" => "15600493277,133",
                "extRefundId" => "1110",
            ]];
        $res = $this->appTest->updateRefundStatus($data);


        $this->assertSame($res['status'], 1);
    }
}
