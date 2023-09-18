<?php
declare(strict_types=1);

use Codeception\Example;
use Codeception\Util\HttpCode;
use Tests\Support\ApiTester;

class CreateEmployeeCest
{
    private $ID;
    public function testAddNewEmployeeWithCorrectData(ApiTester $apiTester): void
    {
        $apiTester->wantToTest('Create new employee');
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
        $apiTester->seeResponseContainsJson(['id' => $this->ID]);
    }
    /** @dataProvider incorrectDataProvider */
    public function testAddEmployeeWithIncorrectData(ApiTester $apiTester, Example $data): void
    {
        $apiTester->wantToTest('Create new employee with incorrect data');

        $apiTester->sendPostAsJson('/api/v1/employee/add', $data['incorrectField']);
        $apiTester->seeResponseCodeIs(HttpCode::BAD_REQUEST);
        $apiTester->seeResponseContainsJson(['message' => 'validation failed']);
    }
    public function incorrectDataProvider(): iterable
    {
        yield [
            'incorrectField' => [
                'name' => 'John Doe',
                'email' => 'johndoeexample.com',
                'position' => 'Software Engineer',
                'age' => 32
            ]
        ];
        yield [
            'incorrectField' => [
                'name' => "",
                'email' => 'johndoe@example.com',
                'position' => 'Software Engineer',
                'age' => 35
            ]
        ];
        yield [
            'incorrectField' => [
                'name' => "John Doe",
                'email' => 'johndoe@example.com',
                'position' => '',
                'age' => 35
            ]
        ];
        yield [
            'incorrectField' => [
                'name' => "John Doe",
                'email' => 'johndoe@example.com',
                'position' => 'Software Engineer',
                'age' => null
            ]
        ];
    }
}

