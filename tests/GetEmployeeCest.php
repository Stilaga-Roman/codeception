<?php
declare(strict_types=1);
use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\Support\ApiTester;

class GetEmployeeCest
{
    private int $ID;
    #[Before('testGetExistingEmployee')]
    public function precondition(ApiTester $apiTester): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'johndoe@example.com',
            'position' => 'Software Engineer',
            'age' => 32
        ];
        $apiTester->sendPostAsJson('/api/v1/employee/add', $data);
        $apiTester->seeResponseCodeIs(HttpCode::CREATED);
        $response = json_decode($apiTester->grabResponse(), true);
        $this->ID = $response['id'];
    }
    public function testGetExistingEmployee(ApiTester $apiTester): void
    {
        $apiTester->wantToTest('Get employee by id');
        $apiTester->sendGET('/api/v1/employee/' . $this->ID);
        $apiTester->seeResponseCodeIs(HttpCode::OK);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseMatchesJsonType([
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'position' => 'string',
            'age' => 'integer'
        ]);
    }
    /** @dataProvider incorrectDataProvider */
    public function testGetNonExistingEmployee(ApiTester $apiTester, Example $data): void
    {
        $apiTester->wantToTest('Get employee with incorrect id');
        $apiTester->sendGET('/api/v1/employee/' . $data['incorrectId']);
        $apiTester->seeResponseCodeIs(HttpCode::NOT_FOUND);
        $apiTester->seeResponseIsJson();
        $apiTester->seeResponseContainsJson(['message' => 'Employee not found']);
    }
    public function incorrectDataProvider(): iterable
    {
        yield [
            'incorrectId' => -1
        ];
    }
}