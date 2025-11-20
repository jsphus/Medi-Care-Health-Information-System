<?php
require_once __DIR__ . '/../config/Database.php';

/**
 * Abstract base class for all entity classes
 * Provides common CRUD operations and enforces OOP principles
 */
abstract class Entity {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Get the table name for this entity
     * @return string
     */
    abstract protected function getTableName(): string;

    /**
     * Get the primary key column name
     * @return string
     */
    abstract protected function getPrimaryKey(): string;

    /**
     * Get all column names for this entity
     * @return array
     */
    abstract protected function getColumns(): array;

    /**
     * Validate entity data
     * @param array $data Data to validate
     * @param bool $isNew Whether this is a new entity
     * @return array Array of error messages (empty if valid)
     */
    abstract protected function validate(array $data, bool $isNew = true): array;

    /**
     * Convert entity to array
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Populate entity from array
     * @param array $data
     * @return self
     */
    abstract public function fromArray(array $data): self;

    /**
     * Save entity (insert or update)
     * @return array ['success' => bool, 'id' => mixed|null, 'errors' => array]
     */
    public function save(): array {
        $data = $this->toArray();
        $primaryKey = $this->getPrimaryKey();
        $primaryKeyValue = $data[$primaryKey] ?? null;

        // Determine if this is an insert or update
        $isNew = empty($primaryKeyValue);

        // Validate
        $errors = $this->validate($data, $isNew);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        try {
            if ($isNew) {
                return $this->insert($data);
            } else {
                return $this->update($data, $primaryKeyValue);
            }
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Database error: ' . $e->getMessage()]];
        }
    }

    /**
     * Insert new entity
     * @param array $data
     * @return array
     */
    protected function insert(array $data): array {
        $tableName = $this->getTableName();
        $columns = $this->getColumns();
        
        // Remove primary key if it's auto-increment
        $insertData = $data;
        $primaryKey = $this->getPrimaryKey();
        if (isset($insertData[$primaryKey]) && empty($insertData[$primaryKey])) {
            unset($insertData[$primaryKey]);
        }

        // Filter to only include valid columns
        $insertData = array_intersect_key($insertData, array_flip($columns));
        
        // Automatically set created_at if column exists and not already set
        if (in_array('created_at', $columns) && (!isset($insertData['created_at']) || $insertData['created_at'] === null)) {
            $insertData['created_at'] = 'NOW()';
        }
        
        // Automatically set updated_at if column exists and not already set
        if (in_array('updated_at', $columns) && (!isset($insertData['updated_at']) || $insertData['updated_at'] === null)) {
            $insertData['updated_at'] = 'NOW()';
        }
        
        // Build SQL - handle NOW() function calls separately
        $fields = [];
        $placeholders = [];
        $params = [];
        
        foreach ($insertData as $field => $value) {
            $fields[] = $field;
            if ($value === 'NOW()') {
                // Use SQL NOW() function directly
                $placeholders[] = 'NOW()';
            } else {
                // Use parameterized placeholder
                $placeholders[] = ':' . $field;
                $params[$field] = $value;
            }
        }

        $sql = "INSERT INTO {$tableName} (" . implode(', ', $fields) . ") 
                VALUES (" . implode(', ', $placeholders) . ")";

        $this->db->execute($sql, $params);
        
        $id = $this->db->lastInsertId($tableName . '_' . $primaryKey . '_seq');
        if (!$id && isset($data[$primaryKey])) {
            $id = $data[$primaryKey];
        }

        return ['success' => true, 'id' => $id];
    }

    /**
     * Update existing entity
     * @param array $data
     * @param mixed $id Primary key value
     * @return array
     */
    protected function update(array $data, $id): array {
        $tableName = $this->getTableName();
        $primaryKey = $this->getPrimaryKey();
        $columns = $this->getColumns();

        // Remove primary key from update data
        $updateData = $data;
        unset($updateData[$primaryKey]);

        // Filter to only include valid columns
        $updateData = array_intersect_key($updateData, array_flip($columns));

        // Automatically set updated_at if column exists and not already set
        if (in_array('updated_at', $columns) && (!isset($updateData['updated_at']) || $updateData['updated_at'] === null)) {
            $updateData['updated_at'] = 'NOW()';
        }

        // Build SQL - handle NOW() function calls separately
        $setClause = [];
        $params = [];
        
        foreach ($updateData as $field => $value) {
            if ($value === 'NOW()') {
                // Use SQL NOW() function directly
                $setClause[] = "{$field} = NOW()";
            } else {
                // Use parameterized placeholder
                $setClause[] = "{$field} = :{$field}";
                $params[$field] = $value;
            }
        }

        if (empty($setClause)) {
            return ['success' => false, 'errors' => ['No fields to update']];
        }

        $sql = "UPDATE {$tableName} SET " . implode(', ', $setClause) . " 
                WHERE {$primaryKey} = :{$primaryKey}";
        
        $params[$primaryKey] = $id;
        $this->db->execute($sql, $params);

        return ['success' => true];
    }

    /**
     * Delete entity by primary key
     * @param mixed $id Primary key value
     * @return array
     */
    public function delete($id = null): array {
        try {
            $primaryKey = $this->getPrimaryKey();
            $tableName = $this->getTableName();
            
            // If no ID provided, try to get from current instance
            if ($id === null) {
                $data = $this->toArray();
                $id = $data[$primaryKey] ?? null;
            }

            if (empty($id)) {
                return ['success' => false, 'errors' => ['ID is required for deletion']];
            }

            $sql = "DELETE FROM {$tableName} WHERE {$primaryKey} = :id";
            $this->db->execute($sql, ['id' => $id]);

            return ['success' => true];
        } catch (Exception $e) {
            return ['success' => false, 'errors' => ['Failed to delete: ' . $e->getMessage()]];
        }
    }

    /**
     * Find entity by primary key
     * @param mixed $id Primary key value
     * @return array|null Associative array or null if not found
     */
    public static function findById($id) {
        $instance = new static();
        $tableName = $instance->getTableName();
        $primaryKey = $instance->getPrimaryKey();
        
        $sql = "SELECT * FROM {$tableName} WHERE {$primaryKey} = :id";
        return $instance->db->fetchOne($sql, ['id' => $id]);
    }

    /**
     * Find all entities with optional conditions
     * @param array $conditions Array of ['field' => 'value'] or ['field' => ['operator' => '=', 'value' => 'val']]
     * @param string $orderBy ORDER BY clause
     * @param int|null $limit Limit number of results
     * @return array Array of associative arrays
     */
    public static function findAll(array $conditions = [], string $orderBy = '', int $limit = null): array {
        $instance = new static();
        $tableName = $instance->getTableName();
        
        $sql = "SELECT * FROM {$tableName}";
        $params = [];

        // Build WHERE clause
        if (!empty($conditions)) {
            $whereClauses = [];
            foreach ($conditions as $field => $value) {
                if (is_array($value) && isset($value['operator']) && isset($value['value'])) {
                    $operator = $value['operator'];
                    $whereClauses[] = "{$field} {$operator} :{$field}";
                    $params[$field] = $value['value'];
                } else {
                    $whereClauses[] = "{$field} = :{$field}";
                    $params[$field] = $value;
                }
            }
            if (!empty($whereClauses)) {
                $sql .= " WHERE " . implode(' AND ', $whereClauses);
            }
        }

        // Add ORDER BY
        if (!empty($orderBy)) {
            $sql .= " ORDER BY {$orderBy}";
        }

        // Add LIMIT
        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            $params['limit'] = $limit;
        }

        return $instance->db->fetchAll($sql, $params);
    }
}

