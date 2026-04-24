<?php
/**
 * InvSys - Model Base
 * 
 * Clase base que proporciona conexión PDO singleton y métodos
 * genéricos para consultas a la base de datos.
 * Todos los modelos deben extender esta clase.
 */

class Model
{
    /**
     * @var PDO|null Instancia singleton de la conexión PDO
     */
    private static ?PDO $connection = null;

    /**
     * @var PDO Referencia a la conexión para uso en instancias
     */
    protected PDO $db;

    /**
     * @var string Nombre de la tabla (debe ser definido por cada modelo hijo)
     */
    protected string $table = '';

    /**
     * @var string Clave primaria de la tabla
     */
    protected string $primaryKey = 'id';

    /**
     * @var string Columna de estado activo para soft-delete.
     * Los modelos hijos pueden sobreescribir (ej: 'activa' para categorías).
     */
    protected string $activeColumn = 'activo';

    // =============================================================
    // VALIDACIÓN DE IDENTIFICADORES SQL
    // =============================================================

    /**
     * Validar que un identificador SQL (nombre de columna/tabla) sea seguro.
     * Solo permite letras, números, guiones bajos y puntos (para tabla.columna).
     * Previene SQL Injection en métodos que interpolan nombres de columna.
     *
     * @param string $identifier Nombre de columna o tabla a validar
     * @return string El identificador validado
     * @throws \InvalidArgumentException Si el identificador contiene caracteres inválidos
     */
    protected function validateIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_.]*$/', $identifier)) {
            throw new \InvalidArgumentException(
                "Identificador SQL inválido: '{$identifier}'. Solo se permiten letras, números, guiones bajos y puntos."
            );
        }
        return $identifier;
    }

    /**
     * Validar múltiples identificadores SQL (array de nombres de columna).
     *
     * @param array $identifiers Lista de nombres de columna
     * @return array Los identificadores validados
     * @throws \InvalidArgumentException Si algún identificador es inválido
     */
    protected function validateIdentifiers(array $identifiers): array
    {
        foreach ($identifiers as $identifier) {
            $this->validateIdentifier($identifier);
        }
        return $identifiers;
    }

    /**
     * Constructor - inicializa la conexión a BD.
     */
    public function __construct()
    {
        $this->db = self::getConnection();
    }

    /**
     * Obtener la conexión PDO singleton.
     *
     * @return PDO
     * @throws \RuntimeException Si la conexión falla
     */
    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            try {
                $config = require CONFIG_PATH . '/database.php';

                $dsn = sprintf(
                    '%s:host=%s;port=%s;dbname=%s;charset=%s',
                    $config['driver'],
                    $config['host'],
                    $config['port'],
                    $config['database'],
                    $config['charset']
                );

                self::$connection = new PDO(
                    $dsn,
                    $config['username'],
                    $config['password'],
                    $config['options']
                );
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    'Error de conexión a la base de datos: ' . $e->getMessage()
                );
            }
        }

        return self::$connection;
    }

    /**
     * Ejecutar consulta SQL con parámetros.
     *
     * @param string $sql Consulta SQL con placeholders
     * @param array $params Parámetros para la consulta
     * @return PDOStatement
     */
    protected function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Ejecutar consulta SQL pública (para queries personalizadas desde controllers).
     *
     * @param string $sql Consulta SQL con placeholders
     * @param array $params Parámetros para la consulta
     * @return array
     */
    public function rawQuery(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Obtener todos los registros de la tabla.
     *
     * @param string $orderBy Columna para ordenar
     * @param string $direction Dirección del orden (ASC, DESC)
     * @return array
     */
    public function findAll(string $orderBy = 'id', string $direction = 'DESC'): array
    {
        $orderBy = $this->validateIdentifier($orderBy);
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';
        $sql = "SELECT * FROM {$this->table} ORDER BY {$orderBy} {$direction}";
        return $this->query($sql)->fetchAll();
    }

    /**
     * Obtener un registro por su ID.
     *
     * @param int $id
     * @return object|false
     */
    public function findById(int $id): object|false
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id LIMIT 1";
        return $this->query($sql, ['id' => $id])->fetch();
    }

    /**
     * Buscar registros por un campo específico.
     *
     * @param string $field Nombre del campo
     * @param mixed $value Valor a buscar
     * @return array
     */
    public function findBy(string $field, mixed $value): array
    {
        $field = $this->validateIdentifier($field);
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value";
        return $this->query($sql, ['value' => $value])->fetchAll();
    }

    /**
     * Buscar un registro por un campo específico.
     *
     * @param string $field Nombre del campo
     * @param mixed $value Valor a buscar
     * @return object|false
     */
    public function findOneBy(string $field, mixed $value): object|false
    {
        $field = $this->validateIdentifier($field);
        $sql = "SELECT * FROM {$this->table} WHERE {$field} = :value LIMIT 1";
        return $this->query($sql, ['value' => $value])->fetch();
    }

    /**
     * Insertar un nuevo registro.
     *
     * @param array $data Datos a insertar [columna => valor]
     * @return int ID del registro insertado
     */
    public function create(array $data): int
    {
        $this->validateIdentifiers(array_keys($data));
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, $data);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Actualizar un registro existente.
     *
     * @param int $id ID del registro
     * @param array $data Datos a actualizar [columna => valor]
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        $this->validateIdentifiers(array_keys($data));
        $setClauses = [];
        foreach (array_keys($data) as $column) {
            $setClauses[] = "{$column} = :{$column}";
        }
        $setString = implode(', ', $setClauses);

        $data['id'] = $id;
        $sql = "UPDATE {$this->table} SET {$setString} WHERE {$this->primaryKey} = :id";

        return $this->query($sql, $data)->rowCount() > 0;
    }

    /**
     * Eliminar un registro por ID.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        return $this->query($sql, ['id' => $id])->rowCount() > 0;
    }

    /**
     * Soft-delete: desactivar un registro en lugar de eliminarlo.
     * Usa la propiedad $activeColumn del modelo (sobreescribible por modelos hijos).
     *
     * @param int $id ID del registro
     * @param string|null $column Nombre de la columna (null = usar $this->activeColumn)
     * @return bool
     */
    public function softDelete(int $id, ?string $column = null): bool
    {
        $col = $column ?? $this->activeColumn;
        $this->validateIdentifier($col);
        return $this->update($id, [$col => 0]);
    }

    /**
     * Toggle: alternar el estado activo/inactivo de un registro.
     * Usa la propiedad $activeColumn del modelo (sobreescribible por modelos hijos).
     *
     * @param int $id ID del registro
     * @param string|null $column Nombre de la columna (null = usar $this->activeColumn)
     * @return bool El nuevo estado (true = activo, false = inactivo)
     */
    public function toggleActive(int $id, ?string $column = null): bool
    {
        $col = $column ?? $this->activeColumn;
        $this->validateIdentifier($col);
        $record = $this->findById($id);
        if (!$record) {
            return false;
        }

        $newStatus = $record->$col ? 0 : 1;
        $this->update($id, [$col => $newStatus]);

        return (bool) $newStatus;
    }

    /**
     * Contar registros con condición opcional.
     *
     * @param string $where Condición WHERE (sin la palabra WHERE)
     * @param array $params Parámetros para la condición
     * @return int
     */
    public function count(string $where = '', array $params = []): int
    {
        $sql = "SELECT COUNT(*) as total FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $result = $this->query($sql, $params)->fetch();
        return (int) $result->total;
    }

    /**
     * Obtener registros con paginación.
     *
     * @param int $page Número de página (empieza en 1)
     * @param int $perPage Registros por página
     * @param string $where Condición WHERE opcional
     * @param array $params Parámetros de la condición
     * @param string $orderBy Columna para ordenar
     * @param string $direction Dirección del orden
     * @return array ['data' => [], 'total' => int, 'pages' => int, 'current' => int]
     */
    public function paginate(
        int $page = 1,
        int $perPage = 15,
        string $where = '',
        array $params = [],
        string $orderBy = 'id',
        string $direction = 'DESC'
    ): array {
        $orderBy = $this->validateIdentifier($orderBy);
        $page = max(1, $page);
        $offset = ($page - 1) * $perPage;
        $direction = strtoupper($direction) === 'ASC' ? 'ASC' : 'DESC';

        // Contar total
        $total = $this->count($where, $params);
        $totalPages = (int) ceil($total / $perPage);

        // Obtener datos
        $sql = "SELECT * FROM {$this->table}";
        if (!empty($where)) {
            $sql .= " WHERE {$where}";
        }
        $sql .= " ORDER BY {$orderBy} {$direction} LIMIT {$perPage} OFFSET {$offset}";

        $data = $this->query($sql, $params)->fetchAll();

        return [
            'data' => $data,
            'total' => $total,
            'pages' => $totalPages,
            'current' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Iniciar una transacción.
     */
    public function beginTransaction(): void
    {
        $this->db->beginTransaction();
    }

    /**
     * Confirmar la transacción.
     */
    public function commit(): void
    {
        $this->db->commit();
    }

    /**
     * Revertir la transacción.
     */
    public function rollback(): void
    {
        $this->db->rollBack();
    }
}
