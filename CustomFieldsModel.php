<?php
namespace Mezon\Service;

/**
 * Class CustomFieldsModel
 *
 * @package Service
 * @subpackage CustomFieldsModel
 * @author Dodonov A.A.
 * @version v.1.0 (2019/11/08)
 * @copyright Copyright (c) 2019, aeon.org
 */

/**
 * Model for processing custom fields
 *
 * @author Dodonov A.A.
 */
class CustomFieldsModel
{

    use \Mezon\PdoCrud\ConnectionTrait;

    /**
     * Table name
     */
    protected $tableName = '';

    /**
     * Constructor
     *
     * @param string $tableName
     *            name of the table
     */
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * Method returns table name
     *
     * @return string Table name
     */
    protected function getCustomFieldsTemplateBame(): string
    {
        return $this->tableName . '_custom_field';
    }

    /**
     * Getting custom fields for object
     *
     * @param int $objectId
     *            Object id
     * @param array $filter
     *            List of required fields or all
     * @return array Result of the fetching
     */
    public function getCustomFieldsForObject(int $objectId, array $filter = [
        '*'
    ]): array
    {
        $result = [];

        $customFields = $this->getConnection()->select(
            '*',
            $this->getCustomFieldsTemplateBame(),
            'object_id = ' . $objectId);

        foreach ($customFields as $field) {
            $fieldName = \Mezon\Functional\Functional::getField($field, 'field_name');

            // if the field in the list or all fields must be fetched
            if (in_array($fieldName, $filter) || in_array('*', $filter)) {
                $result[$fieldName] = \Mezon\Functional\Functional::getField($field, 'field_value');
            }
        }

        return $result;
    }

    /**
     * Deleting custom fields for object
     *
     * @param int $objectId
     *            Object id
     * @param array $filter
     *            List of required fields or all
     */
    public function deleteCustomFieldsForObject(int $objectId, array $filter = [
        '1=1'
    ])
    {
        $condition = implode(' AND ', array_merge($filter, [
            'object_id = ' . $objectId
        ]));

        $this->getConnection()->delete($this->getCustomFieldsTemplateBame(), $condition);
    }

    /**
     * Method sets custom field
     *
     * @param int $objectId
     *            Object id
     * @param string $fieldName
     *            Field name
     * @param string $fieldValue
     *            Field value
     */
    public function setFieldForObject(int $objectId, string $fieldName, string $fieldValue): void
    {
        $connection = $this->getConnection();

        $objectId = intval($objectId);
        $fieldName = htmlspecialchars($fieldName);
        $fieldValue = htmlspecialchars($fieldValue);
        $record = [
            'field_value' => $fieldValue
        ];

        if (count($this->getCustomFieldsForObject($objectId, [
            $fieldName
        ])) > 0) {
            $connection->update(
                $this->getCustomFieldsTemplateBame(),
                $record,
                'field_name LIKE "' . $fieldName . '" AND object_id = ' . $objectId);
        } else {
            // in the previous line we have tried to update unexisting field, so create it
            $record['field_name'] = $fieldName;
            $record['object_id'] = $objectId;
            $connection->insert($this->getCustomFieldsTemplateBame(), $record);
        }
    }

    /**
     * Method fetches custom fields for record
     *
     * @param array $records
     *            List of records
     * @return array Transformed records
     */
    public function getCustomFieldsForRecords(array $records): array
    {
        foreach ($records as $i => $record) {
            $records[$i]['custom'] = $this->getCustomFieldsForObject(\Mezon\Functional\Functional::getField($record, 'id'));
        }

        return $records;
    }
}
