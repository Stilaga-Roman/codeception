<?php
declare(strict_types=1);

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\Support\ApiTester;



class RemoveEmployeeCest
{
    private int $ID;
    #[Before('deleteEmployeeWithCorrectId')]
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
    public function deleteEmployeeWithCorrectId(ApiTester $apiTester): void
    {
        $apiTester->wantToTest('Delete employee by correct id');

        $apiTester->sendDelete('/api/v1/employee/remove/'.$this->ID);
        $apiTester->seeResponseCodeIs(HttpCode::NO_CONTENT);

        $apiTester->sendGet('/api/v1/employee/'.$this->ID);
        $apiTester->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }
    /** @dataProvider incorrectDataProvider */
    public function deleteEmployeeWithIncorrectId(ApiTester $apiTester, Example $data): void
    {
        $apiTester->wantToTest('Delete employee by incorrect id');

        $apiTester->sendDelete('/api/v1/employee/remove/'.$data['incorrectId']);

        $apiTester->seeResponseCodeIs(HttpCode::NOT_FOUND);
    }

    public function incorrectDataProvider(): iterable
    {
        yield [
            'incorrectId' => -1
        ];
        yield [
            'incorrectId' => 'asd'
        ];
    }
}
