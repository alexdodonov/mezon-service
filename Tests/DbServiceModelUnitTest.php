<?php

class DbServiceModelUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Test data for testConstructor test
     *
     * @return array
     */
    public function constructorTestData(): array
    {
        return [
            [
                [
                    'id' => [
                        'type' => 'intger',
                    ],
                ],
                'id',
            ],
            [
                '*',
                '*',
            ],
            [
                new \Mezon\Gui\FieldsAlgorithms([
                    'id' => [
                        'type' => 'intger'
                    ]
                ]),
                'id',
            ],
        ];
    }

    /**
     * Testing constructor
     *
     * @param mixed $data
     *            Parameterfor constructor
     * @param string $origin
     *            original data for validation
     * @dataProvider constructorTestData
     */
    public function testConstructor($data, string $origin)
    {
        // setup and test body
        $model = new \Mezon\Service\DbServiceModel($data, 'entity_name');

        // assertions
        $this->assertTrue($model->hasField($origin), 'Invalid contruction');
    }

    /**
     * Testing constructor with exception
     */
    public function testConstructorException()
    {
        // setup and test body
        $this->expectException(Exception::class);
        new \Mezon\Service\DbServiceModel(new stdClass(), 'entity_name');
    }
}
