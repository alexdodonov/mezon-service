<?php

class DbServiceModelUnitTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Method returns field set
     *
     * @return array field set
     */
    private function getFieldSet(): array
    {
        return [
            'id' => [
                'type' => 'integer',
            ],
        ];
    }

    /**
     * Test data for testConstructor test
     *
     * @return array
     */
    public function constructorTestData(): array
    {
        return [
            [
                $this->getFieldSet(),
                'id',
            ],
            [
                '*',
                '*',
            ],
            [
                new \Mezon\Gui\FieldsAlgorithms($this->getFieldSet()),
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
        $model = new \Mezon\Service\DbServiceModel($data, 'entity_name_constructor');

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
        $model = new \Mezon\Service\DbServiceModel(new stdClass(), 'entity_name');
        var_dump($model);
    }

    /**
     * Testing getFieldsNames method
     */
    public function testGetFieldsNames(): void
    {
        // setup and test body
        $model = new \Mezon\Service\DbServiceModel($this->getFieldSet(), 'entity_name');

        // assertions
        $this->assertEquals('id', implode('', $model->getFields()));
    }

    /**
     * Testing tricky path of setting table name
     */
    public function testSetTableName(): void
    {
        // setup and test body
        $model = new \Mezon\Service\DbServiceModel($this->getFieldSet(), 'entity-name');

        // assertions
        $this->assertEquals('`entity-name`', $model->getTableName());
    }
}
