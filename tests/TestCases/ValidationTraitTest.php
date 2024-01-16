<?php

namespace Silencenjoyer\LaravelValidatableModel\Tests\TestCases;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Silencenjoyer\LaravelValidatableModel\ValidationTrait;
use Orchestra\Testbench\TestCase;

class ValidationTraitTest extends TestCase
{
    protected Model $model;

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->model = $this->createModel();
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->model);
    }

    /**
     * The method creates a model that utilizes the
     * {@see ValidationTrait} for testing purposes.
     *
     * @return Model
     */
    protected function createModel(): Model
    {
        return new class extends Model
        {
            use ValidationTrait;

            /** @var string[] */
            protected $fillable = [
                'name',
                'email',
            ];

            /**
             * {@inheritDoc}
             * @return array
             */
            public function rules(): array
            {
                return [
                    'name' => [
                        'required',
                        'max:75',
                        'min:2',
                        'regex:/^([\p{L}\'`]*[\s\-]?){1,3}$/u',
                        'not_regex:/[-`\']{2,}/'
                    ],
                    'email' => 'email',
                ];
            }

            /**
             * {@inheritDoc}
             * @return array
             */
            public function fields(): array
            {
                return $this->attributes;
            }
        };
    }

    /**
     * This is valid data provider for success validation tests.
     *
     * @return array[]
     */
    public function validData(): array
    {
        return [
            [
                [
                    'name' => 'Jeanne d\'Arc',
                    'email' => 'an_gebrich@outlook.com',
                ],
            ],
            [
                [
                    'name' => 'Otto von Bismarck',
                    'email' => 'an.gebrich@outlook.com',
                ],
            ],
            [
                [
                    'name' => 'Михаил Салтыков-Щедрин',
                    'email' => 'gebrich@gmail.com',
                ],
            ],
            [
                [
                    'name' => 'Вася Пупкін',
                    'email' => 'developer-test@gmail.com',
                ],
            ],
            [
                [
                    'name' => 'Klaus',
                    'email' => 'developer2-test@gmail.com',
                ],
            ]
        ];
    }

    /**
     * This data provider contains single invalid input for tests.
     *
     * @return array[]
     */
    public function singleInvalidData(): array
    {
        return [
            [
                [
                    'name' => 'Test Name For Making tests',
                    'email' => 'test.com',
                ],
            ],
        ];
    }

    /**
     * This data provider contains invalid inputs for tests that are expected
     * to fail during validation.
     *
     * @return array
     */
    public function invalidData(): array
    {
        return array_merge(
            $this->singleInvalidData(),
            [
                [
                    [
                        'name' => '```',
                        'email' => 'myemail',
                    ],
                ],
                [
                    [
                        'name' => 'A',
                        'email' => 's',
                    ],
                ],
            ]
        );
    }

    /**
     * Testing validation with incorrect data.
     *
     * @dataProvider invalidData
     * @param array $data
     * @return void
     */
    public function testThrowingValidationExceptionAsExpected(array $data): void
    {
        $this->expectException(ValidationException::class);

        $this->model->fill($data);
        $this->model->validate();
    }

    /**
     * Testing no exception on valid data with active throwing.
     *
     * @dataProvider validData
     * @param array $data
     * @return void
     */
    public function testNoExceptionWhileCorrectData(array $data): void
    {
        $this->model->fill($data);

        $this->assertTrue(
            $this->model->validate()
        );
    }

    /**
     * Testing validation with correct data.
     *
     * @dataProvider validData
     * @depends testNoExceptionWhileCorrectData
     * @param array $data
     * @return void
     */
    public function testSuccessValidationWithoutExceptionThrowing(array $data): void
    {
        $this->model->fill($data);

        $this->assertTrue($this->model->validate(false));
    }

    /**
     * Testing validation with incorrect data without throwing exception.
     *
     * @dataProvider invalidData
     * @param array $data
     * @return void
     */
    public function testFailedValidationWithoutExceptionThrowing(array $data): void
    {
        $this->model->fill($data);

        $this->assertFalse($this->model->validate(false));
        $errors = $this->model->getErrors()->toArray();
        $this->assertArrayHasKey('name', $errors);
        $this->assertArrayHasKey('email', $errors);
    }

    /**
     * Testing the ability to verify model validation errors after handling
     * a {@see ValidationException}.
     *
     * @dataProvider singleInvalidData
     * @depends testThrowingValidationExceptionAsExpected
     * @param array $data
     * @return void
     */
    public function testErrorMessagesAfterHandlingException(array $data): void
    {
        $this->model->fill($data);

        try {
            $this->model->validate();
        } catch (ValidationException $e) {
            $errors = $this->model->getErrors()->toArray();
            $this->assertArrayHasKey('name', $errors);
            $this->assertArrayHasKey('email', $errors);
        }
    }
}
