<?php

namespace app\tests\integration;

use IntegrationTester;

class SmokeIntegrationCest
{
    public function smokeTestDbConnection(IntegrationTester $I): void
    {
        $result = \Yii::$app->db->createCommand('SELECT 1 AS one')->queryScalar();
        $I->assertEquals(1, (int)$result);
    }
}
