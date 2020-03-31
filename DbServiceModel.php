<?php
namespace Mezon\Service;

/**
 * Class DbServiceModel
 *
 * @package Service
 * @subpackage DbServiceModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/10/18)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Default DB model for the service
 *
 * @author Dodonov A.A.
 */
class DbServiceModel extends \Mezon\Service\ServiceModel
{

    use \Mezon\PdoCrud\ConnectionTrait;

    /**
     * Table name
     */
    protected $tableName = '';

    /**
     * Fields algorithms
     */
    protected $fieldsAlgorithms = false;

    /**
     * Entity name
     */
    protected $entityName = false;

    /**
     * Constructor
     *
     * @param string|array $fields
     *            fields of the model
     * @param string $tableName
     *            name of the table
     * @param string $entityName
     *            name of the entity
     */
    public function __construct($fields = '*', string $tableName = '', string $entityName = '')
    {
        $this->setTableName($tableName);

        $this->entityName = $entityName;

        if (is_string($fields)) {
            // TODO think how exclude \Mezon\Gui\FieldsAlgorithms to separate package
            $this->fieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms(
                [
                    '*' => [
                        'type' => 'string',
                        'title' => 'All fields'
                    ]
                ],
                $tableName);
        } elseif (is_array($fields)) {
            $this->fieldsAlgorithms = new \Mezon\Gui\FieldsAlgorithms($fields, $tableName);
        } elseif ($fields instanceof \Mezon\Gui\FieldsAlgorithms) {
            $this->fieldsAlgorithms = $fields;
        } else {
            throw (new \Exception('Invalid fields description', - 1));
        }
    }

    /**
     * Method sets table name
     *
     * @param string $tableName
     *            Table name
     */
    protected function setTableName(string $tableName = ''): void
    {
        if (strpos($tableName, '-') !== false && strpos($tableName, '`') === false) {
            $tableName = "`$tableName`";
        }

        $this->tableName = $tableName;
    }

    /**
     * Method returns table name
     *
     * @return string table name
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * Method returns list of all fields as string
     *
     * @return string list of all fields as string
     */
    public function getFieldsNames(): string
    {
        return implode(', ', $this->fieldsAlgorithms->getFieldsNames());
    }

    /**
     * Method returns true if the field exists
     *
     * @param string $fieldName
     *            Field name
     * @return bool
     */
    public function hasField(string $fieldName): bool
    {
        return $this->fieldsAlgorithms->hasField($fieldName);
    }

    /**
     * Method returns true if the custom field exists
     *
     * @return bool
     */
    public function hasCustomFields(): bool
    {
        return $this->fieldsAlgorithms->hasCustomFields();
    }

    /**
     * Method validates if the field $field exists
     *
     * @param string $field
     *            Field name
     */
    public function validateFieldExistance(string $field)
    {
        return $this->fieldsAlgorithms->validateFieldExistance($field);
    }

    /**
     * Method returns fields list
     *
     * @return array Fields list
     */
    public function getFields(): array
    {
        return $this->fieldsAlgorithms->getFieldsNames();
    }

    /**
     * Method returns entity name
     *
     * @return string Entity name
     */
    public function getEntityName(): string
    {
        return $this->entityName;
    }

    /**
     * Method returns field type
     *
     * @param string $fieldName
     *            field name
     * @return string field type
     */
    public function getFieldType(string $fieldName): string
    {
        return $this->fieldsAlgorithms->getObject($fieldName)->getType();
    }
}
