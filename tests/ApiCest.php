<?php
namespace Tests;

use Tests\Support\ApiTester;

class ApiCest 
{    
    public function tryApi(ApiTester $I)
    {
        $I->sendGet('/');
        $I->seeResponseCodeIs(200);
        $I->seeResponseIsJson();
    }
}