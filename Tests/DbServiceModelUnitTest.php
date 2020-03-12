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
                        'type' => 'integer',
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
                        'type' => 'integer'
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
        $this->assertEquals($origin, $model->getFieldsNames());
        $this->assertFalse($model->hasCustomFields(), 'Invalid contruction');
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

    /**
     * Testing getFieldsNames method
     */
    /*public function testGetFieldsNames():void{
        // setup and test body
        $model = new \Mezon\Service\DbServiceModel([
            'id' => [
                'type' => 'integer',
            ],
        ], 'entity_name');

        // assertions
    }*/
}
