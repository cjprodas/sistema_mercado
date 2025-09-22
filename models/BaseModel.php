<?php
/**
 * Modelo Base del Sistema
 * Contiene funcionalidades comunes para todos los modelos
 */

class BaseModel
{
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    protected $hidden = [];
    protected $dates = ['created_at', 'updated_at'];
    protected $casts = [];

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Obtener todos los registros
     */
    public function all($columns = '*', $orderBy = null)
    {
        $sql = "SELECT $columns FROM {$this->table}";

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        return $this->db->fetchAll($sql);
    }

    /**
     * Buscar por ID
     */
    public function find($id, $columns = '*')
    {
        $sql = "SELECT $columns FROM {$this->table} WHERE {$this->primaryKey} = ?";
        return $this->db->fetch($sql, [$id]);
    }

    /**
     * Buscar por condición
     */
    public function where($column, $operator, $value = null, $columns = '*')
    {
        if ($value === null) {
            $value = $operator;
            $operator = '=';
        }

        $sql = "SELECT $columns FROM {$this->table} WHERE $column $operator ?";
        return $this->db->fetchAll($sql, [$value]);
    }

    /**
     * Buscar primer registro por condición
     */
    public function first($column, $operator, $value = null, $columns = '*')
    {
        $results = $this->where($column, $operator, $value, $columns);
        return !empty($results) ? $results[0] : null;
    }

    /**
     * Crear nuevo registro
     */
    public function create($data)
    {
        // Filtrar solo campos permitidos
        $data = $this->filterFillable($data);

        // Agregar timestamps si están definidos
        if (in_array('created_at', $this->dates)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        if (in_array('updated_at', $this->dates)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";

        $this->db->query($sql, array_values($data));

        $insertId = $this->db->lastInsertId();

        // Registrar en auditoría
        $this->logChange('INSERT', null, $data, $insertId);

        return $insertId;
    }

    /**
     * Actualizar registro
     */
    public function update($id, $data)
    {
        // Obtener datos anteriores para auditoría
        $oldData = $this->find($id);

        // Filtrar solo campos permitidos
        $data = $this->filterFillable($data);

        // Agregar timestamp de actualización
        if (in_array('updated_at', $this->dates)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }

        $setPairs = [];
        foreach (array_keys($data) as $column) {
            $setPairs[] = "$column = ?";
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $setPairs) . " WHERE {$this->primaryKey} = ?";

        $params = array_merge(array_values($data), [$id]);
        $result = $this->db->query($sql, $params);

        // Registrar en auditoría
        $this->logChange('UPDATE', $oldData, $data, $id);

        return $result->rowCount();
    }

    /**
     * Eliminar registro (soft delete si está configurado)
     */
    public function delete($id)
    {
        // Obtener datos para auditoría
        $oldData = $this->find($id);

        if (in_array('estado', $this->fillable)) {
            // Soft delete
            $result = $this->update($id, ['estado' => 'inactivo']);
        } else {
            // Hard delete
            $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
            $result = $this->db->query($sql, [$id]);
            $result = $result->rowCount();
        }

        // Registrar en auditoría
        $this->logChange('DELETE', $oldData, null, $id);

        return $result;
    }

    /**
     * Contar registros
     */
    public function count($column = null, $operator = null, $value = null)
    {
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $params = [];

        if ($column && $operator && $value !== null) {
            $sql .= " WHERE $column $operator ?";
            $params[] = $value;
        }

        return $this->db->count($sql, $params);
    }

    /**
     * Obtener registros con paginación
     */
    public function paginate($page = 1, $perPage = RECORDS_PER_PAGE, $conditions = [], $orderBy = null)
    {
        $offset = ($page - 1) * $perPage;

        // Construir WHERE clause
        $whereClause = '';
        $params = [];

        if (!empty($conditions)) {
            $whereParts = [];
            foreach ($conditions as $condition) {
                $whereParts[] = "{$condition['column']} {$condition['operator']} ?";
                $params[] = $condition['value'];
            }
            $whereClause = ' WHERE ' . implode(' AND ', $whereParts);
        }

        // Construir ORDER BY clause
        $orderClause = $orderBy ? " ORDER BY $orderBy" : '';

        // Contar total
        $countSql = "SELECT COUNT(*) FROM {$this->table}$whereClause";
        $totalRecords = $this->db->count($countSql, $params);

        // Obtener registros
        $sql = "SELECT * FROM {$this->table}$whereClause$orderClause LIMIT $perPage OFFSET $offset";
        $records = $this->db->fetchAll($sql, $params);

        return [
            'data' => $records,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total_records' => $totalRecords,
                'total_pages' => ceil($totalRecords / $perPage),
                'has_previous' => $page > 1,
                'has_next' => $page < ceil($totalRecords / $perPage)
            ]
        ];
    }

    /**
     * Buscar registros con múltiples condiciones
     */
    public function search($conditions, $columns = '*', $orderBy = null, $limit = null)
    {
        $whereParts = [];
        $params = [];

        foreach ($conditions as $condition) {
            $column = $condition['column'];
            $operator = $condition['operator'];
            $value = $condition['value'];

            if (strtoupper($operator) === 'LIKE') {
                $whereParts[] = "$column LIKE ?";
                $params[] = "%$value%";
            } else {
                $whereParts[] = "$column $operator ?";
                $params[] = $value;
            }
        }

        $sql = "SELECT $columns FROM {$this->table}";

        if (!empty($whereParts)) {
            $sql .= ' WHERE ' . implode(' AND ', $whereParts);
        }

        if ($orderBy) {
            $sql .= " ORDER BY $orderBy";
        }

        if ($limit) {
            $sql .= " LIMIT $limit";
        }

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Ejecutar consulta SQL personalizada
     */
    public function query($sql, $params = [])
    {
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Ejecutar consulta y obtener primer resultado
     */
    public function queryFirst($sql, $params = [])
    {
        return $this->db->fetch($sql, $params);
    }

    /**
     * Filtrar solo campos permitidos
     */
    protected function filterFillable($data)
    {
        if (empty($this->fillable)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Ocultar campos sensibles
     */
    protected function hideFields($data)
    {
        if (empty($this->hidden) || !is_array($data)) {
            return $data;
        }

        foreach ($this->hidden as $field) {
            unset($data[$field]);
        }

        return $data;
    }

    /**
     * Aplicar casting a los datos
     */
    protected function castAttributes($data)
    {
        if (empty($this->casts) || !is_array($data)) {
            return $data;
        }

        foreach ($this->casts as $field => $type) {
            if (isset($data[$field])) {
                switch ($type) {
                    case 'int':
                    case 'integer':
                        $data[$field] = (int) $data[$field];
                        break;
                    case 'float':
                    case 'double':
                        $data[$field] = (float) $data[$field];
                        break;
                    case 'string':
                        $data[$field] = (string) $data[$field];
                        break;
                    case 'bool':
                    case 'boolean':
                        $data[$field] = (bool) $data[$field];
                        break;
                    case 'array':
                    case 'json':
                        $data[$field] = json_decode($data[$field], true);
                        break;
                    case 'date':
                        $data[$field] = new DateTime($data[$field]);
                        break;
                }
            }
        }

        return $data;
    }

    /**
     * Registrar cambios para auditoría
     */
    protected function logChange($action, $oldData, $newData, $recordId)
    {
        // Solo registrar si hay un usuario autenticado
        if (!isset($_SESSION['user_id'])) {
            return;
        }

        try {
            $this->db->query(
                "INSERT INTO historial_cambios 
                 (tabla, registro_id, accion, datos_anteriores, datos_nuevos, usuario_id, ip_address, user_agent) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)",
                [
                    $this->table,
                    $recordId,
                    $action,
                    $oldData ? json_encode($oldData) : null,
                    $newData ? json_encode($newData) : null,
                    $_SESSION['user_id'],
                    $_SERVER['REMOTE_ADDR'] ?? null,
                    $_SERVER['HTTP_USER_AGENT'] ?? null
                ]
            );
        } catch (Exception $e) {
            // Log error but don't break the main operation
            error_log("Error logging change: " . $e->getMessage());
        }
    }

    /**
     * Validar datos según reglas
     */
    public function validate($data, $rules)
    {
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            $fieldRules = explode('|', $rule);

            foreach ($fieldRules as $fieldRule) {
                $ruleParts = explode(':', $fieldRule);
                $ruleName = $ruleParts[0];
                $ruleValue = $ruleParts[1] ?? null;

                switch ($ruleName) {
                    case 'required':
                        if (empty($value)) {
                            $errors[$field] = "El campo $field es requerido";
                        }
                        break;

                    case 'min':
                        if (strlen($value) < $ruleValue) {
                            $errors[$field] = "El campo $field debe tener al menos $ruleValue caracteres";
                        }
                        break;

                    case 'max':
                        if (strlen($value) > $ruleValue) {
                            $errors[$field] = "El campo $field no puede tener más de $ruleValue caracteres";
                        }
                        break;

                    case 'email':
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "El campo $field debe ser un email válido";
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($value)) {
                            $errors[$field] = "El campo $field debe ser numérico";
                        }
                        break;

                    case 'unique':
                        $table = $ruleValue ?: $this->table;
                        $existing = $this->first($field, $value);
                        if ($existing) {
                            $errors[$field] = "El valor del campo $field ya existe";
                        }
                        break;
                }
            }
        }

        return $errors;
    }

    /**
     * Comenzar transacción
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }

    /**
     * Confirmar transacción
     */
    public function commit()
    {
        return $this->db->commit();
    }

    /**
     * Rollback transacción
     */
    public function rollback()
    {
        return $this->db->rollback();
    }

    /**
     * Obtener último error
     */
    public function getLastError()
    {
        $errorInfo = $this->db->getConnection()->errorInfo();
        return $errorInfo[2] ?? null;
    }

    /**
     * Preparar datos para vista (aplicar hiding y casting)
     */
    public function prepareForView($data)
    {
        if (is_array($data) && isset($data[0])) {
            // Array de registros
            return array_map(function ($record) {
                return $this->castAttributes($this->hideFields($record));
            }, $data);
        } else {
            // Registro único
            return $this->castAttributes($this->hideFields($data));
        }
    }
}