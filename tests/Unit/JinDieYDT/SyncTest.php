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

class SyncTest extends \PHPUnit\Framework\TestCase
{
    protected $appTest;

    protected function setUp(): void
    {
        parent::setUp();
        $testCofnig = require 'testConfig.php';

        $config = [
            'driver' => 'JinDieSS',
            'channels' => [
                'JinDieSS' => $testCofnig['JinDieSS'],
            ],
        ];

        $this->appTest = (new PosApi($config))->gateway('JinDieSS');
        $redisAdapter = new FilesystemAdapter();

        $this->appTest->setCache($redisAdapter);
    }

    /**
     * 获取门店.
     */
    public function testQueryShop()
    {
        $data['data'] = [
            'FormId' => 'ORG_Organizations',
            'TopRowCount' => 0,
            'Limit' => 0,
            'StartRow' => 0,
            'FilterString' => '',
            'OrderString' => '',
            'FieldKeys' => 'FOrgID,FNUMBER,FNAME',
        ];

        $res = $this->appTest->queryShop($data);

        $this->assertSame($res['Result'], true);
    }

    /**
     * 查询门店商品
     */
    public function testQueryFood()
    {
        $data = ['data' => [
            'FormId' => 'DE_DIN_Food',
            'TopRowCount' => 0,
            'Limit' => 0,
            'StartRow' => 0,
            'FilterString' => "FUseOrgId='1'",
            'OrderString' => '',
            'FieldKeys' => 'FNUMBER,FNAME,FPrice,FUseOrgId,FUseOrgId.FNUMBER',
        ]];
        $res = $this->appTest->queryProduct($data);
        $this->assertSame(json_decode($res, true)['Result'], true);
    }
}
