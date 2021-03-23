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
            'driver' => 'JinDieXingKong',
            'channels' => [
                'JinDieXingKong' => $testConfig['JinDieXingKong'],
            ],
        ];

        $this->appTest = (new PosApi($config))->gateway('JinDieXingKong');

        $redisAdapter = new FilesystemAdapter();

        $this->appTest->setCache($redisAdapter);
    }

    public function testGetEatsun()
    {
        $data = [
            [
                'FMobile' => '13944702711',
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
                'FCreateOrgId' => 1,    // 创建组织 id
                'FGuestName' => '测试2021', // 姓名
                'FMobile' => '13944702711', // 手机号
                'FGuestLevel' => 'aa5c8bc7-7e68-4dff-8296-1b3fe569c1fe',    // 等级 id
                'FCertNo' => '19900909',    // 身份正好 可选
                'FBusnissStaffId' => 16394, // 业务员 可选
                'FPassword' => '123456',    // 卡密码  可选
                'FCardTypeId' => '577e7f58-0c9e-41ae-a3af-1849abd92be6',    // 卡类型 id
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
                'FGuestCardNo' => 'BM0011',
                'FPOSFlowId' => '20211027008',
                'FModuPricipalAmt' => 200,
                'FModuPresentAmt' => 10,
                'FModuScore' => 20,
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
                'FDate' => '2021-03-15 22:49:25',
                'FGuestCardNo' => 'BM0011',
                'FOpSourceType' => '9',
                'FReceiptItems' => [
                    [
                        'FPayType' => 49,
                        'FReceiptID' => 149874,
                        'FAmt' => 5,
                    ],
                ],
                'FDirectionId' => 1,
                'FPOSFlowId' => '20211027011',
                'FPOSBillId' => time(),
                'FPOSBillNo' => time(),
                'FPOSBillAmt' => 5,
                'FShopDay' => '2021-02-22 00:00:00',
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
}
