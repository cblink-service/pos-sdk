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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MemberTest extends \PHPUnit\Framework\TestCase
{
    protected $appTest;

    protected function setUp(): void
    {
        parent::setUp();

        $testConfig = require 'testConfig.php';
        $config = [
            'driver' => 'KingDeeXK',
            'channels' => [
                'KingDeeXK' => $testConfig['KingDeeXK'],
            ],
        ];

        $this->appTest = (new PosApi($config))->gateway('KingDeeXK');

        $redisAdapter = new FilesystemAdapter();

        $this->appTest->setCache($redisAdapter);
    }

    public function testGetEatsun()
    {
        $data = [
            [
                'FMobile' => '13944702732',
                'FOrgId' => 1,
            ],
        ];

        $res = $this->appTest->queryMember($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 会员卡类型.
     */
    public function testCardType()
    {
        $data = ['PostData' => []];
        $res = $this->appTest->queryGetCardType($data);
        $this->assertSame(json_decode($res, true)['Result'], true);
    }

    /**
     * 客户级别.
     */
    public function testQueryLevel()
    {
        $data = ['PostData' => []];
        $res = $this->appTest->queryGuestLevel($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 添加会员与会员卡
     */
    public function testAddMember()
    {
        $data = [
            [
                'FCreateOrgId' => 1006,    // 创建组织 id
                'FGuestName' => 'kingTestA', // 姓名
                'FMobile' => '18814452218', // 手机号
                'FGuestLevel' => 'aa5c8bc7-7e68-4dff-8296-1b3fe569c1fe',    // 等级 id
                'FCertNo' => '19900909',    // 身份正好 可选
                'FBusnissStaffId' => 16394, // 业务员 可选
                'FPassword' => '123456',    // 卡密码  可选
                'FCardTypeId' => '123',    // 卡类型 id
                'FGenderId' => 0,   // 性别 0 男 1 女
                'FBirthday' => '1990-09-09',    // 生日 可选
                'FEndTime' => '2100-10-10',  // 会员卡有效期 可选
            ],
        ];

        $res = $this->appTest->createMember($data);

        $this->assertSame($res['Result'], true);
    }

    public function testChangeBalance()
    {
        $data = [
            [
                'FOrgId' => '1',
                'FGuestCardNo' => '1',
                'FPOSFlowId' => '202110270010',
                'FModuPricipalAmt' => 2000,
                'FModuPresentAmt' => 1000,
                'FModuScore' => 2000,
                'FModuUnInvoicedAmount' => 20,
                'FNote' => 'test002',
            ],
        ];

        $res = $this->appTest->changeBalance($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 会员卡消费.
     */
    public function testMemberBalanceConsume()
    {
        $data = [
            [
                'FOrgId' => '1',
                'FDate' => '2021-03-18 22:49:25',
                'FGuestCardNo' => '1',
                'FOpSourceType' => '9',
                'FReceiptItems' => [
                    [
                        'FPayType' => 49,
                        'FReceiptID' => 149874,
                        'FAmt' => 1,
                    ],
                ],
                'FDirectionId' => 2,
                'FPOSFlowId' => '20211027018',
                'FPOSBillId' => time(),
                'FPOSBillNo' => time(),
                'FPOSBillAmt' => 1,
                'FShopDay' => '2021-03-18 00:00:00',
            ],
        ];
        $res = $this->appTest->memberBalanceConsume($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 修改客户信息.
     */
    public function testSaveMember()
    {
        $data = [
            [
                'FGuestId' => '126467',
                'FDescription' => 'test',
                'FGender' => '1',
                'FBirthday' => '2021-06-25',
                'FMobile' => '13944702711',
                'FName' => '测试202103',
            ],
        ];

        $res = $this->appTest->saveMember($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 查询账户明细.
     */
    public function testQueryMemberCardRecord()
    {
        $data = [
            [
                'MAXFID' => '0',
                'CARDNUMBER' => 'BM0011',
                'JOURNALTYPE' => '0',
            ],
        ];

        $res = $this->appTest->queryMemberCardRecord($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     *
     */
    public function testQueryMemberCoupon()
    {
        $data = [
            [
                'FGuestId' => '138096',
                'FIsUsed' => '0',
                'FIsOutdated' => '0',
                //'FRuleIds' => '0',
            ],
        ];

        $res = $this->appTest->memberCoupons($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     *
     */
    public function testQueryCoupon()
    {
        $data = [
            [
                'FCouponMedium' => '2',
                'FCouponType' => '4',
                'FCouponKind' => '1',
                'OnlyInDate' => '1',
                // 'FName' =>'仅限于券包'
            ],
        ];
//        $data = [
//            "FId" => 100092,    // 券规则ID
//            "FGuestId"=> 138096 //
//        ];

        $res = $this->appTest->queryCoupons($data);

        $this->assertSame($res['Result'], true);
    }

    public function testSendCoupon()
    {
        $data = [
            [
                'FGuestId' => '138096',
                'FBatchRuleId' => '100095'
            ],
        ];

        $res = $this->appTest->sendCoupon($data);

        var_dump($res);exit;
    }

    public function testConsume()
    {
        $data = [
            [
                'FOrgId' => '1',
                'FCouponNo' => '000020588088377',
                'FPosFlowId' => time(),
                'FOpState' => '1',
                // 'FCouponUseChannel' => '',
            ],
        ];

        $res = $this->appTest->consume($data);

        $this->assertSame($res['Result'], true);
    }
}
