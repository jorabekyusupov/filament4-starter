<?php

namespace Modules\App\Libraries\Batch;

use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;

class Batch implements BatchInterface
{
    /**
     * @var DatabaseManager
     */
    protected $db;

    public function __construct(DatabaseManager $db)
    {
        $this->db = $db;
    }

    /**
     * Update multiple rows
     * @param Model $table
     * @param array $values
     * @param string $index
     * @updatedBy Ibrahim Sakr <ebrahimes@gmail.com>
     *
     * @desc
     * Example
     * $table = 'users';
     * $value = [
     *     [
     *         'id' => 1,
     *         'status' => 'active',
     *         'nickname' => 'Mohammad'
     *     ] ,
     *     [
     *         'id' => 5,
     *         'status' => 'deactive',
     *         'nickname' => 'Ghanbari'
     *     ] ,
     * ];
     * $index = 'id';
     *
     * @return bool|int
     */
    public function update(Model $table, array $values, string $index = null, bool $raw = false)
    {
        if (!count($values)) {
            return false;
        }

        if (!isset($index) || empty($index)) {
            $index = $table->getKeyName();
        }

        $tableName = $this->getFullTableName($table);
        $connection = $this->db->connection($this->getConnectionName($table));

        // Building the case statement for each field
        $updates = [];
        foreach ($values as $value) {
            foreach ($value as $field => $fieldValue) {
                if ($field !== $index) {
                    if (!$raw) {
                        $fieldValue = ($fieldValue === null)
                            ? 'NULL'
                            : $connection->getPdo()->quote((string) $fieldValue);
                    }
                    $updates[$field][] = "WHEN {$index} = '{$value[$index]}' THEN {$fieldValue}";
                }
            }
        }

        // Building the final SQL query
        $sql = "UPDATE {$tableName} SET ";
        $updateLines = [];
        foreach ($updates as $field => $cases) {
            $updateLines[] = "{$field} = CASE " . implode(" ", $cases) . " ELSE {$field} END";
        }
        $sql .= implode(', ', $updateLines);
        $sql .= " WHERE {$index} IN (" . implode(',', array_map(function ($v) use ($connection, $index) {
                return $connection->getPdo()->quote($v[$index]);
            }, $values)) . ");";


        return $connection->update($sql);
    }

    /**
     * Update multiple rows
     * @param Model $table
     * @param array $values
     * @param string $index
     * @param string|null $index2
     * @param bool $raw
     * @return bool|int
     * @updatedBy Ibrahim Sakr <ebrahimes@gmail.com>
     *
     * @desc
     * Example
     * $table = 'users';
     * $value = [
     *     [
     *         'id' => 1,
     *         'status' => 'active',
     *         'nickname' => 'Mohammad'
     *     ] ,
     *     [
     *         'id' => 5,
     *         'status' => 'deactive',
     *         'nickname' => 'Ghanbari'
     *     ] ,
     * ];
     * $index = 'id';
     * $index2 = 'user_id';
     *
     */
    public function updateWithTwoIndex(Model $table, array $values, string $index = null, string $index2 = null, bool $raw = false)
    {
        if (!count($values)) {
            return false;
        }

        if (!isset($index) || empty($index)) {
            $index = $table->getKeyName();
        }
        if (!isset($index2)) {
            // Handle error or throw exception if second index is mandatory
            throw new \InvalidArgumentException("Second index `$index2` is mandatory.");
        }

        $tableName = $this->getFullTableName($table);
        $connection = $this->db->connection($this->getConnectionName($table));

        // Building the case statement for each field
        $updates = [];
        foreach ($values as $value) {
            foreach ($value as $field => $fieldValue) {
                if ($field !== $index) {
                    if ($field === $index2) {
                        continue;
                    }
                    $fieldValue = $raw ? $fieldValue : $connection->getPdo()->quote($fieldValue);
                    $keyValue = $connection->getPdo()->quote($value[$index]);
                    $key2Value = $connection->getPdo()->quote($value[$index2]);
                    $updates[$field][] = "WHEN {$index} = {$keyValue} AND {$index2} = {$key2Value} THEN {$fieldValue}";
                }
            }
        }

        // Building the final SQL query
        $sql = "UPDATE {$tableName} SET ";
        $updateLines = [];
        foreach ($updates as $field => $cases) {
            $updateLines[] = "{$field} = CASE " . implode(" ", $cases) . " ELSE {$field} END";
        }
        $sql .= implode(', ', $updateLines);

        // Build the WHERE clause to optimize the query
        $whereIds = implode(',', array_map(fn($v) => $connection->getPdo()->quote($v[$index]), $values));
        $whereIds2 = implode(',', array_map(fn($v) => $connection->getPdo()->quote($v[$index2]), $values));
        $sql .= " WHERE {$index} IN ({$whereIds}) AND {$index2} IN ({$whereIds2});";

        return $connection->update($sql);
    }

    /**
     * Insert Multi rows
     * @param Model $table
     * @param array $columns
     * @param array $values
     * @param int $batchSize
     * @param bool $insertIgnore
     * @return bool|mixed
     * @throws \Throwable
     * @updatedBy Ibrahim Sakr <ebrahimes@gmail.com>
     *
     * @desc
     * Example
     *
     * $table = 'users';
     * $columns = [
     *      'firstName',
     *      'lastName',
     *      'email',
     *      'isActive',
     *      'status',
     * ];
     * $values = [
     *     [
     *         'Mohammad',
     *         'Ghanbari',
     *         'emailSample_1@gmail.com',
     *         '1',
     *         '0',
     *     ] ,
     *     [
     *         'Saeed',
     *         'Mohammadi',
     *         'emailSample_2@gmail.com',
     *         '1',
     *         '0',
     *     ] ,
     *     [
     *         'Avin',
     *         'Ghanbari',
     *         'emailSample_3@gmail.com',
     *         '1',
     *         '0',
     *     ] ,
     * ];
     * $batchSize = 500; // insert 500 (default), 100 minimum rows in one query
     */
    public function insert(Model $table, array $columns, array $values, int $batchSize = 500, bool $insertIgnore = false)
    {


        if (count($columns) != count($values[0])) {
            return false;
        }

        $tableName = $this->getFullTableName($table);
        $connection = $this->db->connection($this->getConnectionName($table));

        $onConflict = $insertIgnore ? "ON CONFLICT DO NOTHING" : "";

        $chunks = array_chunk($values, $batchSize);
        foreach ($chunks as $chunk) {
            $rowsSql = [];
            foreach ($chunk as $row) {
                $pdo = $connection->getPdo();

                $quoted = array_map(function ($value) use ($pdo) {
                    if ($value === null) {
                        return 'NULL'; // MUHIM: qo‘shtirnoqsiz!
                    }
                    return $pdo->quote((string) $value);
                }, $row);
                $rowsSql[] = '(' . implode(', ', $quoted) . ')';
            }

            $sql = "INSERT INTO {$tableName} (" . implode(', ', $columns) . ") VALUES " . implode(', ', $rowsSql) . " {$onConflict};";
            $connection->statement($sql);
        }

        return true;
    }

    /**
     * @param Model $model
     * @return string
     * @author Ibrahim Sakr <ebrahimes@gmail.com>
     */
    private function getFullTableName(Model $model)
    {
        return $model->getConnection()->getTablePrefix() . $model->getTable();
    }

    /**
     * @param Model $model
     * @return string|null
     * @author Ibrahim Sakr <ebrahimes@gmail.com>
     */
    private function getConnectionName(Model $model)
    {
        if (!is_null($cn = $model->getConnectionName()))
            return $cn;

        return $model->getConnection()->getName();
    }
}
